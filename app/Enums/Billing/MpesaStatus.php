<?php

namespace App\Enums\Billing;

enum MpesaStatus: string
{
    case Initiated = 'initiated';
    case Pending   = 'pending';
    case Completed = 'completed';
    case Failed    = 'failed';
    case Cancelled = 'cancelled';
    case Timeout   = 'timeout';

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Failed,
            self::Cancelled,
            self::Timeout,
        ]);
    }
}
