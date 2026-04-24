<?php

namespace App\Enums\Billing;

enum PaymentMethod: string
{
    case Cash      = 'cash';
    case Mpesa     = 'mpesa';
    case Nhif      = 'nhif';
    case Sha       = 'sha';
    case Insurance = 'insurance';
    case Corporate = 'corporate';
    case Waiver    = 'waiver';

    public function label(): string
    {
        return match ($this) {
            self::Cash      => 'Cash',
            self::Mpesa     => 'M-Pesa',
            self::Nhif      => 'NHIF',
            self::Sha       => 'SHA',
            self::Insurance => 'Insurance',
            self::Corporate => 'Corporate / Company',
            self::Waiver    => 'Waiver',
        };
    }

    public function requiresReference(): bool
    {
        return in_array($this, [
            self::Mpesa,
            self::Nhif,
            self::Sha,
            self::Insurance,
            self::Corporate,
        ]);
    }

    public function isInsuranceType(): bool
    {
        return in_array($this, [
            self::Nhif,
            self::Sha,
            self::Insurance,
            self::Corporate,
        ]);
    }
}
