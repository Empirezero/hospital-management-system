<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock',
        'expiry_date',
        'description',
        'image',
    ];

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
    }