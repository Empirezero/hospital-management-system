<?php

namespace App\Models;

use App\Enums\Billing\ClaimType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InsuranceProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'short_name',
        'type',
        'contact_person',
        'phone',
        'email',
        'address',
        'claim_submission_method',
        'claim_email',
        'portal_url',
        'credit_limit_days',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'type'              => ClaimType::class,
        'credit_limit_days' => 'integer',
        'is_active'         => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function insuranceClaims(): HasMany
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByType(Builder $query, ClaimType $type): Builder
    {
        return $query->where('type', $type);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getDisplayNameAttribute(): string
    {
        return $this->short_name ?? $this->name;
    }
}
