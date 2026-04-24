<?php

namespace App\Models;

use App\Enums\Billing\ClaimStatus;
use App\Enums\Billing\ClaimType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class InsuranceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        // Existing fields — kept intact
        'sale_id',
        'patient_id',
        'submitted_by',
        'insurer_name',
        'policy_number',
        'member_number',
        'claimed_amount',
        'approved_amount',
        'patient_copay',
        'status',
        'submitted_at',
        'response_date',
        'payment_date',
        'due_date',
        'rejection_reason',
        'notes',
        'payment_reference',
        'reviewed_by',

        // New billing fields
        'bill_id',
        'insurance_provider_id',
        'claim_number',
        'claim_type',
        'scheme_name',
        'principal_member_name',
        'relationship_to_principal',
        'card_expiry_date',
        'paid_amount',
        'insurer_reference',
        'approved_at',
        'settled_at',
        'rejected_at',
    ];

    protected $casts = [
        // Existing casts
        'submitted_at'    => 'date',
        'response_date'   => 'date',
        'payment_date'    => 'date',
        'due_date'        => 'date',
        'claimed_amount'  => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'patient_copay'   => 'decimal:2',

        // New casts
        'claim_type'      => ClaimType::class,
        'card_expiry_date' => 'date',
        'paid_amount'     => 'decimal:2',
        'approved_at'     => 'datetime',
        'settled_at'      => 'datetime',
        'rejected_at'     => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    // Existing relationships — kept intact
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // New relationships
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function insuranceProvider(): BelongsTo
    {
        return $this->belongsTo(InsuranceProvider::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', 'submitted');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['submitted', 'under_review']);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereIn('status', ['approved', 'partial']);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    // New scopes
    public function scopeForBill(Builder $query, int $billId): Builder
    {
        return $query->where('bill_id', $billId);
    }

    public function scopeByType(Builder $query, ClaimType $type): Builder
    {
        return $query->where('claim_type', $type);
    }

    // ─── Workflow Helpers ─────────────────────────────────────────────────────

    public function submit(int $submittedBy): void
    {
        $this->update([
            'status'       => 'submitted',
            'submitted_by' => $submittedBy,
            'submitted_at' => now(),
        ]);
    }

    public function approve(float $approvedAmount, int $reviewedBy, ?string $insurerReference = null): void
    {
        $copay = max(0, $this->claimed_amount - $approvedAmount);

        $this->update([
            'status'           => $approvedAmount >= $this->claimed_amount ? 'approved' : 'partial',
            'approved_amount'  => $approvedAmount,
            'patient_copay'    => $copay,
            'reviewed_by'      => $reviewedBy,
            'insurer_reference' => $insurerReference,
            'response_date'    => now(),
            'approved_at'      => now(),
        ]);

        // Update bill's insurance_covered if linked to a bill
        if ($this->bill_id) {
            $this->bill->update(['insurance_covered' => $approvedAmount]);
            $this->bill->recalculate();
        }
    }

    public function reject(string $reason, int $reviewedBy): void
    {
        $this->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by'      => $reviewedBy,
            'response_date'    => now(),
            'rejected_at'      => now(),
        ]);
    }

    public function markPaid(float $paidAmount, string $paymentReference): void
    {
        $this->update([
            'status'            => 'paid',
            'paid_amount'       => $paidAmount,
            'payment_reference' => $paymentReference,
            'payment_date'      => now(),
            'settled_at'        => now(),
        ]);
    }

    // ─── Attributes ───────────────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'        => 'badge-secondary',
            'submitted'    => 'badge-primary',
            'under_review' => 'badge-info',
            'approved'     => 'badge-success',
            'partial'      => 'badge-warning',
            'rejected'     => 'badge-danger',
            'paid'         => 'badge-success',
            'appealed'     => 'badge-warning',
            default        => 'badge-secondary',
        };
    }

    public function getIsBillClaimAttribute(): bool
    {
        return $this->bill_id !== null;
    }

    public function getIsPharmacyClaimAttribute(): bool
    {
        return $this->sale_id !== null;
    }
}
