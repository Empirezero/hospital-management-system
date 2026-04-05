<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'patient_number', 'date_of_birth', 'gender',
        'blood_group', 'phone', 'address',
        'emergency_contact_name', 'emergency_contact_phone',
        'allergies', 'chronic_conditions',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()         { return $this->belongsTo(User::class); }
    public function appointments() { return $this->hasMany(Appointment::class); }
    public function encounters()   { return $this->hasMany(Encounter::class); }
    public function prescriptions(){ return $this->hasMany(Prescription::class); }

    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            $year  = now()->year;
            $count = static::whereYear('created_at', $year)->count() + 1;
            $patient->patient_number = 'PAT-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }
}