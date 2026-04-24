<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_number',
        'bill_id',
        'payment_id',
        'issued_by',
        'voided_by',
        'patient_name',
        'patient_number',
        'bill_number',
        'payment_method',
        'amount_received',
        'bill_total',
        'balance_before',
        'balance_after',
        'issued_at',
        'voided_at',
        'void_reason',
    ];

    protected $casts = [
        'amount_received' => 'decimal:2',
        'bill_total'      => 'decimal:2',
        'balance_before'  => 'decimal:2',
        'balance_after'   => 'decimal:2',
        'issued_at'       => 'datetime',
        'voided_at'       => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeValid(Builder $query): Builder
    {
        return $query->whereNull('voided_at');
    }

    // ─── Factory ──────────────────────────────────────────────────────────────

    /**
     * Create a receipt from a confirmed payment.
     * Snapshots patient name, bill number, and payment method at issue time.
     */
    public static function issueFor(Payment $payment, int $issuedBy): self
    {
        $bill    = $payment->bill;
        $patient = $payment->patient;

        $balanceBefore = $bill->balance_due + $payment->amount;

        return static::create([
            'receipt_number'  => static::generateNumber(),
            'bill_id'         => $bill->id,
            'payment_id'      => $payment->id,
            'issued_by'       => $issuedBy,
            'patient_name'    => $patient->full_name,
            'patient_number'  => $patient->patient_number ?? null,
            'bill_number'     => $bill->bill_number,
            'payment_method'  => $payment->payment_method->label(),
            'amount_received' => $payment->amount,
            'bill_total'      => $bill->total_amount,
            'balance_before'  => $balanceBefore,
            'balance_after'   => $bill->balance_due,
            'issued_at'       => now(),
        ]);
    }

    public function void(string $reason, int $voidedBy): void
    {
        $this->update([
            'voided_at'   => now(),
            'voided_by'   => $voidedBy,
            'void_reason' => $reason,
        ]);
    }

    public function getIsVoidAttribute(): bool
    {
        return $this->voided_at !== null;
    }

    public static function generateNumber(): string
    {
        $latest = static::max('id') ?? 0;
        return 'RCP-' . date('Y') . '-' . str_pad($latest + 1, 5, '0', STR_PAD_LEFT);
    }
}
