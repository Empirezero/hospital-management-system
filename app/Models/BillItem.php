<?php

namespace App\Models;

use App\Enums\Billing\BillItemType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id',
        'item_type',
        'service_id',
        'lab_test_id',
        'prescription_item_id',
        'description',
        'quantity',
        'unit_price',
        'nhif_rate',
        'discount_percent',
        'discount_amount',
        'line_total',
        'is_insurance_covered',
        'insurance_amount',
        'patient_portion',
        'ward_name',
        'admission_date',
        'discharge_date',
        'days',
        'notes',
    ];

    protected $casts = [
        'item_type'            => BillItemType::class,
        'quantity'             => 'decimal:2',
        'unit_price'           => 'decimal:2',
        'nhif_rate'            => 'decimal:2',
        'discount_percent'     => 'decimal:2',
        'discount_amount'      => 'decimal:2',
        'line_total'           => 'decimal:2',
        'insurance_amount'     => 'decimal:2',
        'patient_portion'      => 'decimal:2',
        'is_insurance_covered' => 'boolean',
        'admission_date'       => 'date',
        'discharge_date'       => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function labTest(): BelongsTo
    {
        return $this->belongsTo(LabTest::class);
    }

 

    // ─── Computed ─────────────────────────────────────────────────────────────

    /**
     * Compute and persist the line_total from current quantity, price, discount.
     * Call after setting unit_price, quantity, or discount fields.
     */
    public function computeAndSave(): void
    {
        $gross = round((float) $this->unit_price * (float) $this->quantity, 2);

        $discountAmount = $this->discount_percent > 0
            ? round($gross * ($this->discount_percent / 100), 2)
            : (float) $this->discount_amount;

        $lineTotal = max(0, $gross - $discountAmount);

        // Insurance covers up to nhif_rate per item if flagged
        $insuranceAmount = 0;
        if ($this->is_insurance_covered && $this->nhif_rate !== null) {
            $insuranceAmount = min((float) $this->nhif_rate * (float) $this->quantity, $lineTotal);
        }

        $this->discount_amount  = $discountAmount;
        $this->line_total       = $lineTotal;
        $this->insurance_amount = $insuranceAmount;
        $this->patient_portion  = max(0, $lineTotal - $insuranceAmount);

        $this->save();
    }

    // ─── Factory Helpers ──────────────────────────────────────────────────────

    /**
     * Create a BillItem from a Service model.
     * Snapshot the price — do NOT reference the service price dynamically later.
     */
    public static function fromService(
        Bill $bill,
        Service $service,
        float $quantity = 1,
        bool $useNhifRate = false,
    ): self {
        $item = new self([
            'bill_id'              => $bill->id,
            'item_type'            => $service->category,
            'service_id'           => $service->id,
            'description'          => $service->name,
            'quantity'             => $quantity,
            'unit_price'           => $service->standard_price,
            'nhif_rate'            => $service->nhif_price,
            'is_insurance_covered' => $useNhifRate && $service->is_nhif_covered,
        ]);

        if ($useNhifRate && $service->is_nhif_covered) {
            $item->unit_price = $service->nhif_price ?? $service->standard_price;
        }

        $item->computeAndSave();

        return $item;
    }

    /**
     * Bed charge helper for inpatients.
     */
    public static function bedCharge(
        Bill $bill,
        string $wardName,
        float $dailyRate,
        \Carbon\Carbon $admissionDate,
        \Carbon\Carbon $dischargeDate,
    ): self {
        $days = max(1, $admissionDate->diffInDays($dischargeDate));

        $item = new self([
            'bill_id'        => $bill->id,
            'item_type'      => BillItemType::Bed,
            'description'    => "Bed charge – {$wardName}",
            'quantity'       => $days,
            'unit_price'     => $dailyRate,
            'ward_name'      => $wardName,
            'admission_date' => $admissionDate,
            'discharge_date' => $dischargeDate,
            'days'           => $days,
        ]);

        $item->computeAndSave();

        return $item;
    }
}
