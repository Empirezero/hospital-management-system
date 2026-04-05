<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'patient_id', 'doctor_id', 'name', 'email',
        'number', 'scheduled_at', 'message', 'notes', 'status', 'type',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function patient()   { return $this->belongsTo(Patient::class); }
    public function doctor()    { return $this->belongsTo(Doctor::class); }
    public function encounter() { return $this->hasOne(Encounter::class); }

    public function scopePending($query)  { return $query->where('status', 'pending'); }
    public function scopeToday($query)    { return $query->whereDate('scheduled_at', today()); }
    public function scopeUpcoming($query) {
        return $query->where('scheduled_at', '>=', now())
                     ->whereIn('status', ['pending', 'confirmed'])
                     ->orderBy('scheduled_at');
    }
}