<?php

namespace App\Enums\Billing;

enum BillType: string
{
    case Outpatient = 'outpatient';
    case Inpatient  = 'inpatient';
    case Emergency  = 'emergency';

    public function label(): string
    {
        return match ($this) {
            self::Outpatient => 'Outpatient',
            self::Inpatient  => 'Inpatient',
            self::Emergency  => 'Emergency',
        };
    }
}
