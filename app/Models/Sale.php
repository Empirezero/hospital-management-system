<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'prescription_id',
        'patient_id',
        'quantity_sold',
        'total_price',
        'user_id',
        'sale_type',
        'payment_method',
        'payment_reference',
        'payment_status',
        'billed_to',
        'bill_due_date',
    ];

    protected $casts = [
        'bill_due_date' => 'date',
        'total_price'   => 'decimal:2',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }
    public function scopeBilled($query)
    {
        return $query->where('payment_status', 'billed');
    }
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
    public function insuranceClaim()
    {
        return $this->hasOne(InsuranceClaim::class);
    }
}
