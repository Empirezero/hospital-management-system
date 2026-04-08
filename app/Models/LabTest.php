<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'is_active',
    ];

    public function requests()
    {
        return $this->hasMany(LabRequest::class);
    }
}
