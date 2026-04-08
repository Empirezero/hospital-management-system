<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabRequest extends Model
{
    protected $fillable = [
        'lab_test_id',
        'doctor_id',
        'user_id',
        'appointment_id',
        'encounter_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'notes',
        'result_file',
        'result_notes',
        'status',
        'released_to_patient',
        'released_at',
        'requested_at',
        'completed_at',
    ];

    protected $casts = [
        'requested_at'        => 'datetime',
        'completed_at'        => 'datetime',
        'released_at'         => 'datetime',
        'released_to_patient' => 'boolean',
    ];

    public function labTest()
    {
        return $this->belongsTo(LabTest::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function encounter()
    {
        return $this->belongsTo(Encounter::class);
    }
}
