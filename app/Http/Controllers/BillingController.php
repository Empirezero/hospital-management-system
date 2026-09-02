<?php

namespace App\Http\Controllers;

use App\Enums\Billing\BillItemType;
use App\Enums\Billing\BillType;
use App\Enums\Billing\PaymentMethod;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Encounter;
use App\Models\InsuranceProvider;
use App\Models\MpesaTransaction;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Services\Billing\BillingService;
use App\Services\Billing\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    public function __construct(
        private readonly BillingService $billing,
        private readonly MpesaService   $mpesa,
    ) {}

// =============Bills=========//
    public function index()
    {
        $bills = Bill::with('patient')
            ->latest()
            ->paginate(20);

        return view('billing.index', compact('bills'));
    }

    public function create()
    {
        $patients   = Patient::orderBy('id')->get();
        $encounters = Encounter::with('patient')
            ->latest()
            ->get();

        return view('billing.create', compact('patients', 'encounters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'   => ['required', 'exists:patients,id'],
            'bill_type'    => ['required', Rule::enum(BillType::class)],
            'encounter_id' => ['nullable', 'exists:encounters,id'],
            'due_date'     => ['nullable', 'date', 'after:today'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $patient   = Patient::findOrFail($data['patient_id']);
        $encounter = isset($data['encounter_id'])
            ? Encounter::find($data['encounter_id'])
            : null;

        $bill = $this->billing->createBill(
            patient: $patient,
            type: BillType::from($data['bill_type']),
            createdBy: $request->user(),
            encounter: $encounter,
            dueDate: isset($data['due_date'])
                ? \Carbon\Carbon::parse($data['due_date'])
                : null,
            notes: $data['notes'] ?? null,
        );

        return redirect()->route('billing.show', $bill)
            ->with('message', "Bill {$bill->bill_number} created successfully.");
    }

    public function show(Bill $bill)
    {
        $bill->load([
            'patient',
            'items.service',
            'payments',
            'receipts',
            'insuranceClaims.insuranceProvider',
        ]);

        $services  = Service::active()->orderBy('name')->get();
        $providers = InsuranceProvider::active()->orderBy('name')->get();

        return view('billing.show', compact('bill', 'services', 'providers'));
    }

    public function open(Bill $bill)
    {
        $this->billing->openBill($bill);

        return redirect()->route('billing.show', $bill)
            ->with('message', "Bill {$bill->bill_number} is now open for payment.");
    }

    public function void(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $this->billing->voidBill($bill, $data['reason'], $request->user());

        return redirect()->route('billing.index')
            ->with('message', "Bill {$bill->bill_number} has been voided.");
    }

    public function applyDiscount(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'notes'            => ['nullable', 'string', 'max:255'],
        ]);

        $this->billing->applyDiscount(
            $bill,
            $data['discount_percent'],
            $data['notes'] ?? null
        );

        return redirect()->route('billing.show', $bill)
            ->with('message', "Discount of {$data['discount_percent']}% applied.");
    }

    // =========================================================================
    // BILL ITEMS
    // =========================================================================

    public function addServiceItem(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'service_id'    => ['required', 'exists:services,id'],
            'quantity'      => ['nullable', 'numeric', 'min:0.01'],
            'use_nhif_rate' => ['nullable', 'boolean'],
            'notes'         => ['nullable', 'string', 'max:255'],
        ]);

        $service = Service::findOrFail($data['service_id']);

        $this->billing->addServiceItem(
            bill: $bill,
            service: $service,
            quantity: $data['quantity'] ?? 1,
            useNhifRate: $data['use_nhif_rate'] ?? false,
            notes: $data['notes'] ?? null,
        );

        return redirect()->route('billing.show', $bill)
            ->with('message', "{$service->name} added to bill.");
    }

    public function addManualItem(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'item_type'            => ['required', Rule::enum(BillItemType::class)],
            'description'          => ['required', 'string', 'max:255'],
            'unit_price'           => ['required', 'numeric', 'min:0'],
            'quantity'             => ['nullable', 'numeric', 'min:0.01'],
            'discount_percent'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_insurance_covered' => ['nullable', 'boolean'],
            'nhif_rate'            => ['nullable', 'numeric', 'min:0'],
            'notes'                => ['nullable', 'string', 'max:255'],
        ]);

        $this->billing->addManualItem(
            bill: $bill,
            type: BillItemType::from($data['item_type']),
            description: $data['description'],
            unitPrice: $data['unit_price'],
            quantity: $data['quantity'] ?? 1,
            discountPercent: $data['discount_percent'] ?? 0,
            isInsuranceCovered: $data['is_insurance_covered'] ?? false,
            nhifRate: $data['nhif_rate'] ?? null,
            notes: $data['notes'] ?? null,
        );

        return redirect()->route('billing.show', $bill)
            ->with('message', "Item added to bill.");
    }

    public function removeItem(Bill $bill, BillItem $item)
    {
        abort_if($item->bill_id !== $bill->id, 404);
        $this->billing->removeItem($bill, $item);

        return redirect()->route('billing.show', $bill)
            ->with('message', "Item removed from bill.");
    }

    // =========================================================================
    // PAYMENTS
    // =========================================================================

    public function recordPayment(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'payment_method'   => ['required', Rule::enum(PaymentMethod::class)],
            'amount'           => ['required', 'numeric', 'min:1'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $method = PaymentMethod::from($data['payment_method']);

        if ($method->requiresReference() && empty($data['reference_number'])) {
            return redirect()->back()
                ->withErrors(['reference_number' => "A reference number is required for {$method->label()} payments."]);
        }

        $result = $this->billing->recordPayment(
            bill: $bill,
            method: $method,
            amount: $data['amount'],
            receivedBy: $request->user(),
            referenceNumber: $data['reference_number'] ?? null,
            notes: $data['notes'] ?? null,
        );

        return redirect()->route('billing.show', $bill)
            ->with('message', "Payment of KES " . number_format($data['amount'], 2) . " recorded. Receipt: {$result['receipt']->receipt_number}");
    }

    public function reversePayment(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        $bill = $payment->bill;
        $this->billing->reversePayment($payment, $data['reason'], $request->user());

        return redirect()->route('billing.show', $bill)
            ->with('message', "Payment reversed successfully.");
    }

    // =========================================================================
    // M-PESA STK PUSH
    // =========================================================================

    public function initiateMpesa(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'phone_number' => ['required', 'string'],
            'amount'       => ['nullable', 'numeric', 'min:1'],
        ]);

        $amount = $data['amount'] ?? $bill->balance_due;

        if ($amount <= 0) {
            return redirect()->back()
                ->withErrors(['amount' => 'Bill has no outstanding balance.']);
        }

        $transaction = $this->mpesa->initiateStk(
            bill: $bill,
            phoneNumber: $data['phone_number'],
            amount: $amount,
        );

        return redirect()->route('billing.show', $bill)
            ->with('message', "M-Pesa prompt sent. Ask patient to enter PIN.")
            ->with('mpesa_transaction_id', $transaction->id);
    }

    public function mpesaStatus(MpesaTransaction $transaction)
    {
        if (
            ! $transaction->status->isTerminal()
            && $transaction->created_at->diffInSeconds(now()) > 30
        ) {
            $this->mpesa->queryStatus($transaction);
            $transaction->refresh();
        }

        return response()->json([
            'status'               => $transaction->status->value,
            'mpesa_receipt_number' => $transaction->mpesa_receipt_number,
            'is_complete'          => $transaction->status->isTerminal(),
            'payment_id'           => $transaction->payment_id,
        ]);
    }
    public function mpesaCallback(Request $request)
    {
        Log::info('M-Pesa callback received', $request->all());

        $this->mpesa->handleCallback($request->all());

        // Safaricom expects this exact response shape regardless of outcome —
        // returning anything else can cause Safaricom to retry the callback repeatedly.
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    // =========================================================================
    // INSURANCE CLAIMS
    // =========================================================================

    public function createClaim(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'insurance_provider_id'     => ['required', 'exists:insurance_providers,id'],
            'member_number'             => ['required', 'string', 'max:100'],
            'claimed_amount'            => ['required', 'numeric', 'min:1'],
            'scheme_name'               => ['nullable', 'string', 'max:255'],
            'principal_member_name'     => ['nullable', 'string', 'max:255'],
            'relationship_to_principal' => ['nullable', 'string', 'max:50'],
            'card_expiry_date'          => ['nullable', 'date'],
        ]);

        $provider = InsuranceProvider::findOrFail($data['insurance_provider_id']);

        $claim = $this->billing->createInsuranceClaim(
            bill: $bill,
            provider: $provider,
            memberNumber: $data['member_number'],
            claimedAmount: $data['claimed_amount'],
            submittedBy: $request->user(),
            details: $data,
        );

        return redirect()->route('billing.show', $bill)
            ->with('message', "Insurance claim {$claim->claim_number} created.");
    }
}
