<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'number',
        'speciality',
        'room',
        'location',
        'image',
        'bio',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function encounters()
    {
        return $this->hasMany(Encounter::class);
    }
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }
}
