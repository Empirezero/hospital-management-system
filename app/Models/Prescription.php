<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'encounter_id',
        'medicine_id',
        'patient_id',
        'doctor_id',
        'dosage',
        'frequency',
        'duration_days',
        'instructions',
        'status',
    ];

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
