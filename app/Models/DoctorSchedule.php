<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_id',
        'day',
        'start_time',
        'end_time',
        'slot_duration',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Generate all time slots for this schedule
    public function getSlots(): array
    {
        $slots  = [];
        $start  = Carbon::createFromTimeString($this->start_time);
        $end    = Carbon::createFromTimeString($this->end_time);

        while ($start->lt($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes($this->slot_duration);
        }

        return $slots;
    }
}
