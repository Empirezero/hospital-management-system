<?php

namespace App\Enums\Billing;

enum ServiceCategory: string
{
    case Consultation = 'consultation';
    case Lab          = 'lab';
    case Pharmacy     = 'pharmacy';
    case Procedure    = 'procedure';
    case Bed          = 'bed';
    case Other        = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Consultation => 'Consultation',
            self::Lab          => 'Laboratory',
            self::Pharmacy     => 'Pharmacy',
            self::Procedure    => 'Procedure / Service',
            self::Bed          => 'Bed Charge',
            self::Other        => 'Other',
        };
    }
}
