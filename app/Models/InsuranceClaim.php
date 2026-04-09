<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsuranceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    protected $casts = [
        'submitted_at'    => 'date',
        'response_date'   => 'date',
        'payment_date'    => 'date',
        'due_date'        => 'date',
        'claimed_amount'  => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'patient_copay'   => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }
    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'under_review']);
    }
    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'partial']);
    }
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

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
}
