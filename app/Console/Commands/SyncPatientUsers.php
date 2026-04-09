<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Console\Command;

class SyncPatientUsers extends Command
{
    protected $signature   = 'patients:sync';
    protected $description = 'Create Patient records for all users with role=patient';

    public function handle()
    {
        $users = User::where('role', 'patient')
            ->whereDoesntHave('patient')
            ->get();

        if ($users->isEmpty()) {
            $this->info('All patient users are already synced.');
            return;
        }

        foreach ($users as $user) {
            Patient::create([
                'user_id' => $user->id,
                'gender'  => null,
            ]);
            $this->info("Created patient record for: {$user->name} ({$user->email})");
        }

        $this->info('Sync complete.');
    }
}