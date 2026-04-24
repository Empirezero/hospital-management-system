<?php

namespace App\Models;

use App\Enums\Billing\MpesaStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaTransaction extends Model
{
    protected $fillable = [
        'payment_id',
        'bill_id',
        'merchant_request_id',
        'checkout_request_id',
        'mpesa_receipt_number',
        'phone_number',
        'amount',
        'account_reference',
        'transaction_desc',
        'status',
        'result_code',
        'result_description',
        'transaction_date',
        'raw_callback',
    ];

    protected $casts = [
        'status'           => MpesaStatus::class,
        'amount'           => 'decimal:2',
        'raw_callback'     => 'array',
        'transaction_date' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Process a Safaricom STK callback payload.
     * Creates a confirmed Payment record on success.
     */
    public function processCallback(array $callbackData): bool
    {
        $body        = $callbackData['Body']['stkCallback'] ?? [];
        $resultCode  = $body['ResultCode'] ?? -1;
        $resultDesc  = $body['ResultDesc'] ?? 'Unknown';

        $this->update([
            'result_code'        => $resultCode,
            'result_description' => $resultDesc,
            'raw_callback'       => $callbackData,
        ]);

        if ($resultCode !== 0) {
            $this->update([
                'status' => match ($resultCode) {
                    1032   => MpesaStatus::Cancelled,  // User cancelled
                    default => MpesaStatus::Failed,
                },
            ]);
            return false;
        }

        // Extract metadata from callback items
        $items = collect($body['CallbackMetadata']['Item'] ?? [])
            ->pluck('Value', 'Name');

        $receiptNumber   = $items->get('MpesaReceiptNumber');
        $transactionDate = $items->get('TransactionDate');

        $this->update([
            'status'                => MpesaStatus::Completed,
            'mpesa_receipt_number'  => $receiptNumber,
            'transaction_date'      => $transactionDate
                ? \Carbon\Carbon::createFromFormat('YmdHis', (string) $transactionDate)
                : now(),
        ]);

        // Create the confirmed Payment record
        $payment = Payment::create([
            'payment_number'  => Payment::generateNumber(),
            'bill_id'         => $this->bill_id,
            'patient_id'      => $this->bill->patient_id,
            'received_by'     => 1, // System user — override in your service layer
            'payment_method'  => 'mpesa',
            'amount'          => $this->amount,
            'status'          => 'confirmed',
            'reference_number' => $receiptNumber,
            'paid_at'         => $this->transaction_date ?? now(),
            'confirmed_at'    => now(),
        ]);

        $this->update(['payment_id' => $payment->id]);

        // Recalculate bill totals
        $this->bill->recalculate();

        return true;
    }

    // ─── Static Helpers ───────────────────────────────────────────────────────

    public static function generateNumber(): string
    {
        // Delegate to a NumberGeneratorService or use a simple DB sequence
        $latest = static::max('id') ?? 0;
        return 'MPESA-' . date('Y') . '-' . str_pad($latest + 1, 5, '0', STR_PAD_LEFT);
    }
}
