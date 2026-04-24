<?php

namespace App\Services\Billing;

use Illuminate\Support\Facades\DB;

class NumberGeneratorService
{
    public function next(string $prefix, int $pad = 5): string
    {
        $year = date('Y');
        $key  = strtoupper($prefix) . '-' . $year;

        $next = DB::transaction(function () use ($key) {
            $sequence = DB::table('sequences')
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            if ($sequence === null) {
                DB::table('sequences')->insert([
                    'key'        => $key,
                    'last_value' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return 1;
            }

            $next = $sequence->last_value + 1;

            DB::table('sequences')
                ->where('key', $key)
                ->update([
                    'last_value' => $next,
                    'updated_at' => now(),
                ]);

            return $next;
        });

        return strtoupper($prefix) . '-' . $year . '-' . str_pad($next, $pad, '0', STR_PAD_LEFT);
    }

    // Convenience wrappers
    public function billNumber(): string
    {
        return $this->next('BILL');
    }
    public function paymentNumber(): string
    {
        return $this->next('PAY');
    }
    public function receiptNumber(): string
    {
        return $this->next('RCP');
    }
    public function claimNumber(): string
    {
        return $this->next('CLM');
    }
}
