<?php

namespace App\Services\Billing;

use App\Enums\Billing\BillStatus;
use App\Enums\Billing\BillType;
use App\Enums\Billing\BillItemType;
use App\Enums\Billing\PaymentMethod;
use App\Enums\Billing\PaymentStatus;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Encounter;
use App\Models\InsuranceClaim;
use App\Models\InsuranceProvider;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillingService
{
    public function __construct(
        private readonly NumberGeneratorService $numbers,
    ) {}

    // =========================================================================
    // BILL CREATION
    // =========================================================================

    public function createBill(
        Patient    $patient,
        BillType   $type,
        User       $createdBy,
        ?Encounter $encounter = null,
        ?Carbon    $dueDate = null,
        ?string    $notes = null,
    ): Bill {
        return DB::transaction(function () use ($patient, $type, $createdBy, $encounter, $dueDate, $notes) {
            return Bill::create([
                'bill_number'  => $this->numbers->billNumber(),
                'bill_type'    => $type,
                'patient_id'   => $patient->id,
                'encounter_id' => $encounter?->id,
                'created_by'   => $createdBy->id,
                'status'       => BillStatus::Draft,
                'due_date'     => $dueDate,
                'notes'        => $notes,
            ]);
        });
    }

    public function createBillFromEncounter(Encounter $encounter, User $createdBy): Bill
    {
        $billType = match ($encounter->type ?? 'outpatient') {
            'inpatient' => BillType::Inpatient,
            'emergency' => BillType::Emergency,
            default     => BillType::Outpatient,
        };

        return $this->createBill(
            patient: $encounter->patient,
            type: $billType,
            createdBy: $createdBy,
            encounter: $encounter,
        );
    }

    public function openBill(Bill $bill): Bill
    {
        $this->assertStatus($bill, BillStatus::Draft, 'Only draft bills can be opened.');
        $bill->recalculate();
        $bill->update(['status' => BillStatus::Open]);
        return $bill->fresh();
    }

    // =========================================================================
    // BILL ITEMS
    // =========================================================================

    public function addServiceItem(
        Bill    $bill,
        Service $service,
        float   $quantity = 1,
        bool    $useNhifRate = false,
        ?string $notes = null,
    ): BillItem {
        $this->assertEditable($bill);

        return DB::transaction(function () use ($bill, $service, $quantity, $useNhifRate, $notes) {
            $item = BillItem::fromService($bill, $service, $quantity, $useNhifRate);
            $item->update(['notes' => $notes]);
            $bill->recalculate();
            return $item;
        });
    }

    public function addManualItem(
        Bill         $bill,
        BillItemType $type,
        string       $description,
        float        $unitPrice,
        float        $quantity = 1,
        float        $discountPercent = 0,
        bool         $isInsuranceCovered = false,
        ?float       $nhifRate = null,
        ?string      $notes = null,
    ): BillItem {
        $this->assertEditable($bill);

        return DB::transaction(function () use (
            $bill,
            $type,
            $description,
            $unitPrice,
            $quantity,
            $discountPercent,
            $isInsuranceCovered,
            $nhifRate,
            $notes
        ) {
            $item = new BillItem([
                'bill_id'              => $bill->id,
                'item_type'            => $type,
                'description'          => $description,
                'quantity'             => $quantity,
                'unit_price'           => $unitPrice,
                'nhif_rate'            => $nhifRate,
                'discount_percent'     => $discountPercent,
                'is_insurance_covered' => $isInsuranceCovered,
                'notes'                => $notes,
            ]);

            $item->computeAndSave();
            $bill->recalculate();

            return $item;
        });
    }

    public function addBedCharge(
        Bill   $bill,
        string $wardName,
        float  $dailyRate,
        Carbon $admissionDate,
        Carbon $dischargeDate,
    ): BillItem {
        return DB::transaction(function () use ($bill, $wardName, $dailyRate, $admissionDate, $dischargeDate) {
            $item = BillItem::bedCharge($bill, $wardName, $dailyRate, $admissionDate, $dischargeDate);
            $bill->recalculate();
            return $item;
        });
    }

    public function removeItem(Bill $bill, BillItem $item): void
    {
        $this->assertEditable($bill);

        DB::transaction(function () use ($bill, $item) {
            $item->delete();
            $bill->recalculate();
        });
    }

    public function applyDiscount(Bill $bill, float $percent, ?string $notes = null): Bill
    {
        $this->assertNotVoid($bill);

        DB::transaction(function () use ($bill, $percent, $notes) {
            $bill->update([
                'discount_percent' => $percent,
                'notes'            => $notes ?? $bill->notes,
            ]);
            $bill->recalculate();
        });

        return $bill->fresh();
    }

    // =========================================================================
    // PAYMENTS
    // =========================================================================

    public function recordCashPayment(
        Bill    $bill,
        float   $amount,
        User    $receivedBy,
        ?string $notes = null,
    ): array {
        return $this->recordPayment(
            bill: $bill,
            method: PaymentMethod::Cash,
            amount: $amount,
            receivedBy: $receivedBy,
            status: PaymentStatus::Confirmed,
            notes: $notes,
        );
    }

    public function recordMpesaManual(
        Bill    $bill,
        float   $amount,
        string  $mpesaCode,
        User    $receivedBy,
        ?string $notes = null,
    ): array {
        return $this->recordPayment(
            bill: $bill,
            method: PaymentMethod::Mpesa,
            amount: $amount,
            receivedBy: $receivedBy,
            status: PaymentStatus::Confirmed,
            referenceNumber: strtoupper(trim($mpesaCode)),
            notes: $notes,
        );
    }

    public function recordInsurancePayment(
        Bill          $bill,
        PaymentMethod $method,
        float         $amount,
        User          $receivedBy,
        string        $referenceNumber,
        ?string       $notes = null,
    ): array {
        if (! $method->isInsuranceType()) {
            throw new \InvalidArgumentException("Payment method {$method->value} is not an insurance type.");
        }

        return $this->recordPayment(
            bill: $bill,
            method: $method,
            amount: $amount,
            receivedBy: $receivedBy,
            status: PaymentStatus::Confirmed,
            referenceNumber: $referenceNumber,
            notes: $notes,
        );
    }

    public function recordPayment(
        Bill          $bill,
        PaymentMethod $method,
        float         $amount,
        User          $receivedBy,
        PaymentStatus $status = PaymentStatus::Confirmed,
        ?string       $referenceNumber = null,
        ?string       $notes = null,
    ): array {
        $this->assertNotVoid($bill);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Payment amount must be greater than zero.');
        }

        if ($amount > $bill->balance_due + 0.01) {
            throw ValidationException::withMessages([
                'amount' => "Payment of KES {$amount} exceeds balance due of KES {$bill->balance_due}.",
            ]);
        }

        return DB::transaction(function () use ($bill, $method, $amount, $receivedBy, $status, $referenceNumber, $notes) {

            $payment = Payment::create([
                'payment_number'   => $this->numbers->paymentNumber(),
                'bill_id'          => $bill->id,
                'patient_id'       => $bill->patient_id,
                'received_by'      => $receivedBy->id,
                'payment_method'   => $method,
                'amount'           => $amount,
                'status'           => $status,
                'reference_number' => $referenceNumber,
                'paid_at'          => now(),
                'confirmed_at'     => $status === PaymentStatus::Confirmed ? now() : null,
                'notes'            => $notes,
            ]);

            $bill->recalculate();
            $bill->refresh();

            $receipt = null;
            if ($status === PaymentStatus::Confirmed) {
                $receipt = $this->issueReceipt($payment, $receivedBy);
            }

            return compact('payment', 'receipt');
        });
    }

    public function reversePayment(Payment $payment, string $reason, User $reversedBy): void
    {
        if ($payment->status !== PaymentStatus::Confirmed) {
            throw new \LogicException('Only confirmed payments can be reversed.');
        }

        DB::transaction(function () use ($payment, $reason, $reversedBy) {
            $payment->reverse($reason, $reversedBy->id);

            if ($receipt = $payment->receipt) {
                $receipt->void("Payment reversed: {$reason}", $reversedBy->id);
            }
        });
    }

    // =========================================================================
    // RECEIPTS
    // =========================================================================

    public function issueReceipt(Payment $payment, User $issuedBy): Receipt
    {
        $bill = $payment->bill;

        return Receipt::create([
            'receipt_number'  => $this->numbers->receiptNumber(),
            'bill_id'         => $bill->id,
            'payment_id'      => $payment->id,
            'issued_by'       => $issuedBy->id,
            'patient_name'    => $bill->patient->user->name ?? $bill->patient->patient_number,
            'patient_number'  => $bill->patient->patient_number ?? null,
            'bill_number'     => $bill->bill_number,
            'payment_method'  => $payment->payment_method->label(),
            'amount_received' => $payment->amount,
            'bill_total'      => $bill->total_amount,
            'balance_before'  => $bill->balance_due + $payment->amount,
            'balance_after'   => $bill->balance_due,
            'issued_at'       => now(),
        ]);
    }

    // =========================================================================
    // INSURANCE CLAIMS
    // =========================================================================

    public function createInsuranceClaim(
        Bill              $bill,
        InsuranceProvider $provider,
        string            $memberNumber,
        float             $claimedAmount,
        User              $submittedBy,
        array             $details = [],
    ): InsuranceClaim {
        $this->assertNotVoid($bill);

        return DB::transaction(function () use ($bill, $provider, $memberNumber, $claimedAmount, $submittedBy, $details) {
            return InsuranceClaim::create([
                'claim_number'              => $this->numbers->claimNumber(),
                'bill_id'                   => $bill->id,
                'patient_id'               => $bill->patient_id,
                'insurance_provider_id'    => $provider->id,
                'insurer_name'             => $provider->name,
                'member_number'             => $memberNumber,
                'claim_type'               => $provider->type,
                'claimed_amount'           => $claimedAmount,
                'status'                   => 'pending',
                'scheme_name'              => $details['scheme_name'] ?? null,
                'principal_member_name'    => $details['principal_member_name'] ?? null,
                'relationship_to_principal' => $details['relationship_to_principal'] ?? 'Self',
                'card_expiry_date'         => $details['card_expiry_date'] ?? null,
                'submitted_by'             => $submittedBy->id,
            ]);
        });
    }

    // =========================================================================
    // VOID
    // =========================================================================

    public function voidBill(Bill $bill, string $reason, User $voidedBy): void
    {
        $this->assertNotVoid($bill);

        if ($bill->confirmedPayments()->exists()) {
            throw new \LogicException('Cannot void a bill with confirmed payments. Reverse the payments first.');
        }

        $bill->void($reason, $voidedBy->id);
    }

    public function writeOff(Bill $bill, string $reason, User $approvedBy): void
    {
        $this->assertNotVoid($bill);

        $bill->update([
            'status'      => BillStatus::WrittenOff,
            'void_reason' => $reason,
            'voided_by'   => $approvedBy->id,
            'voided_at'   => now(),
        ]);
    }

    // =========================================================================
    // GUARDS
    // =========================================================================

    private function assertEditable(Bill $bill): void
    {
        if (! $bill->status->isEditable()) {
            throw new \LogicException(
                "Bill {$bill->bill_number} is {$bill->status->label()} and cannot be edited. Only draft bills can be modified."
            );
        }
    }

    private function assertNotVoid(Bill $bill): void
    {
        if (in_array($bill->status, [BillStatus::Void, BillStatus::WrittenOff])) {
            throw new \LogicException("Bill {$bill->bill_number} is {$bill->status->label()} and cannot be modified.");
        }
    }

    private function assertStatus(Bill $bill, BillStatus $expected, string $message): void
    {
        if ($bill->status !== $expected) {
            throw new \LogicException($message);
        }
    }
}
