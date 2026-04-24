<?php

namespace App\Enums\Billing;

enum ClaimStatus: string
{
    case Pending   = 'pending';
    case Submitted = 'submitted';
    case Approved  = 'approved';
    case Partial   = 'partial';
    case Rejected  = 'rejected';
    case Paid      = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Pending Submission',
            self::Submitted => 'Submitted',
            self::Approved  => 'Approved',
            self::Partial   => 'Partially Approved',
            self::Rejected  => 'Rejected',
            self::Paid      => 'Settled',
        };
    }
}
