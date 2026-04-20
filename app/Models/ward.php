<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    protected $fillable = [
        'name',
        'type',
        'total_beds',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_beds' => 'integer',
    ];

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function availableBeds(): HasMany
    {
        return $this->hasMany(Bed::class)->where('status', 'available');
    }
}
