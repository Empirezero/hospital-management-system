<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function isDoctor()
    {
        return $this->role === 'doctor';
    }
    public function isPatient()
    {
        return $this->role === 'patient';
    }
    public function isPharmacist()
    {
        return $this->role === 'pharmacist';
    }
    public function isNurse()
    {
        return $this->role === 'nurse';
    }
    public function isLabTechnician()
    {
        return $this->role === 'lab_technician';
    }
    
    protected static function booted(): void
    {
        static::created(function (User $user) {
            if ($user->role === 'patient') {
                \App\Models\Patient::firstOrCreate(
                    ['user_id' => $user->id],
                    ['gender'  => null]
                );
            }
            // Auto-link Doctor record by name
            if ($user->role === 'doctor') {
                \App\Models\Doctor::where('name', $user->name)
                    ->whereNull('user_id')
                    ->update(['user_id' => $user->id]);
            }
        });
        
    }
}
