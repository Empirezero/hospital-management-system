<?php

namespace App\Enums\Billing;

enum PaymentStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Failed    = 'failed';
    case Reversed  = 'reversed';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Failed    => 'Failed',
            self::Reversed  => 'Reversed',
        };
    }
}
