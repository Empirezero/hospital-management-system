<?php

namespace App\Enums\Billing;

enum BillStatus: string
{
    case Draft      = 'draft';
    case Open       = 'open';
    case Partial    = 'partial';
    case Paid       = 'paid';
    case Void       = 'void';
    case WrittenOff = 'written_off';

    public function label(): string
    {
        return match($this) {
            self::Draft      => 'Draft',
            self::Open       => 'Open',
            self::Partial    => 'Partially Paid',
            self::Paid       => 'Paid',
            self::Void       => 'Void',
            self::WrittenOff => 'Written Off',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft      => 'gray',
            self::Open       => 'blue',
            self::Partial    => 'yellow',
            self::Paid       => 'green',
            self::Void       => 'red',
            self::WrittenOff => 'orange',
        };
    }

    public function isSettleable(): bool
    {
        return in_array($this, [self::Open, self::Partial]);
    }

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }
}