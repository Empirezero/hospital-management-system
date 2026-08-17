<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    protected $fillable = [
        'bed_id',
        'ward_id',
        'doctor_id',
        'admitted_by',
        'appointment_id',
        'patient_name',
        'patient_email',
        'patient_phone',
        'reason',
        'notes',
        'status',
        'admitted_at',
        'discharged_at'
    ];

    protected $casts = [
        'admitted_at'   => 'datetime',
        'discharged_at' => 'datetime',
    ];

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function discharge(): bool
    {
        return $this->update([
            'status'        => 'discharged',
            'discharged_at' => now(),
        ]);
    }

    public function admittedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'admitted_by');
    }
    }
