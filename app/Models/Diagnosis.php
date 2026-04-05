<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Diagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'encounter_id', 'icd_code', 'description', 'severity', 'notes',
    ];

    public function encounter() { return $this->belongsTo(Encounter::class); }
}