<?php

namespace App\Models;

use App\Enums\Billing\ServiceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'category',
        'standard_price',
        'nhif_price',
        'is_nhif_covered',
        'nhif_code',
        'unit',
        'department',
        'description',
        'is_active',
    ];

    protected $casts = [
        'category'        => ServiceCategory::class,
        'standard_price'  => 'decimal:2',
        'nhif_price'      => 'decimal:2',
        'is_nhif_covered' => 'boolean',
        'is_active'       => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function billItems(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeNhifCovered(Builder $query): Builder
    {
        return $query->where('is_nhif_covered', true);
    }

    public function scopeByCategory(Builder $query, ServiceCategory $category): Builder
    {
        return $query->where('category', $category);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Return the effective price for a given payment context.
     * SHA/NHIF patients use the nhif_price if available.
     */
    public function effectivePrice(bool $isNhifPatient = false): float
    {
        if ($isNhifPatient && $this->is_nhif_covered && $this->nhif_price !== null) {
            return (float) $this->nhif_price;
        }

        return (float) $this->standard_price;
    }
}
