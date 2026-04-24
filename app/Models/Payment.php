<?php

namespace App\Models;

use App\Enums\Billing\PaymentMethod;
use App\Enums\Billing\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{
    protected $fillable = [
        'payment_number',
        'bill_id',
        'patient_id',
        'received_by',
        'reversed_by',
        'payment_method',
        'amount',
        'status',
        'reference_number',
        'bank_name',
        'cheque_number',
        'paid_at',
        'confirmed_at',
        'reversed_at',
        'reversal_reason',
        'notes',
    ];

    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'status'         => PaymentStatus::class,
        'amount'         => 'decimal:2',
        'paid_at'        => 'datetime',
        'confirmed_at'   => 'datetime',
        'reversed_at'    => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function mpesaTransaction(): HasOne
    {
        return $this->hasOne(MpesaTransaction::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', PaymentStatus::Confirmed);
    }

    public function scopeByMethod(Builder $query, PaymentMethod $method): Builder
    {
        return $query->where('payment_method', $method);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function confirm(): void
    {
        $this->update([
            'status'       => PaymentStatus::Confirmed,
            'confirmed_at' => now(),
        ]);

        // Trigger bill recalculation
        $this->bill->recalculate();
    }

    public function reverse(string $reason, int $reversedBy): void
    {
        $this->update([
            'status'          => PaymentStatus::Reversed,
            'reversed_by'     => $reversedBy,
            'reversed_at'     => now(),
            'reversal_reason' => $reason,
        ]);

        // Trigger bill recalculation
        $this->bill->recalculate();
    }

    public function getIsMpesaAttribute(): bool
    {
        return $this->payment_method === PaymentMethod::Mpesa;
    }
}
