<?php

namespace App\Models;

use App\Enums\Billing\BillStatus;
use App\Enums\Billing\BillType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Bill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bill_number',
        'bill_type',
        'patient_id',
        'encounter_id',
        'created_by',
        'voided_by',
        'status',
        'subtotal',
        'discount_amount',
        'discount_percent',
        'tax_amount',
        'insurance_covered',
        'total_amount',
        'amount_paid',
        'balance_due',
        'due_date',
        'paid_at',
        'voided_at',
        'void_reason',
        'notes',
    ];

    protected $casts = [
        'bill_type'        => BillType::class,
        'status'           => BillStatus::class,
        'subtotal'         => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'insurance_covered' => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'amount_paid'      => 'decimal:2',
        'balance_due'      => 'decimal:2',
        'due_date'         => 'date',
        'paid_at'          => 'datetime',
        'voided_at'        => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function confirmedPayments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('status', 'confirmed');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function insuranceClaims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public function mpesaTransactions(): HasMany
    {
        return $this->hasMany(MpesaTransaction::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForPatient(Builder $query, int $patientId): Builder
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [BillStatus::Open, BillStatus::Partial]);
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('balance_due', '>', 0)
            ->whereNotIn('status', [BillStatus::Void, BillStatus::WrittenOff]);
    }

    public function scopeByStatus(Builder $query, BillStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    // ─── Financial Helpers ────────────────────────────────────────────────────

    /**
     * Recalculate all totals from bill_items and confirmed payments.
     * Call this whenever items or payments change.
     */
    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('line_total');

        $discountAmount = $this->discount_percent > 0
            ? round($subtotal * ($this->discount_percent / 100), 2)
            : ($this->discount_amount ?? 0);

        $insuranceCovered = $this->items()->sum('insurance_amount');

        $totalAmount = max(0, $subtotal - $discountAmount - $insuranceCovered + $this->tax_amount);

        $amountPaid = $this->confirmedPayments()->sum('amount');

        $balanceDue = max(0, $totalAmount - $amountPaid);

        $this->update([
            'subtotal'          => $subtotal,
            'discount_amount'   => $discountAmount,
            'insurance_covered' => $insuranceCovered,
            'total_amount'      => $totalAmount,
            'amount_paid'       => $amountPaid,
            'balance_due'       => $balanceDue,
        ]);

        $this->syncStatus();
    }

    /**
     * Update status based on current balance.
     */
    public function syncStatus(): void
    {
        if (in_array($this->status, [BillStatus::Void, BillStatus::WrittenOff])) {
            return; // Never auto-change out of terminal states
        }

        $newStatus = match (true) {
            $this->balance_due <= 0 && $this->amount_paid > 0 => BillStatus::Paid,
            $this->amount_paid > 0 && $this->balance_due > 0  => BillStatus::Partial,
            $this->status === BillStatus::Draft                => BillStatus::Draft,
            default                                            => BillStatus::Open,
        };

        if ($newStatus !== $this->status) {
            $this->update([
                'status'  => $newStatus,
                'paid_at' => $newStatus === BillStatus::Paid ? now() : null,
            ]);
        }
    }

    /**
     * Mark the bill as void. Cannot be undone.
     */
    public function void(string $reason, int $voidedBy): void
    {
        $this->update([
            'status'     => BillStatus::Void,
            'void_reason' => $reason,
            'voided_by'  => $voidedBy,
            'voided_at'  => now(),
        ]);
    }

    // ─── Computed Attributes ──────────────────────────────────────────────────

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->balance_due <= 0 && $this->status === BillStatus::Paid;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && $this->status->isSettleable();
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'KES ' . number_format($this->total_amount, 2);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'KES ' . number_format($this->balance_due, 2);
    }
}
