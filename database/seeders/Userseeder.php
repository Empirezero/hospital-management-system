<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin User',      'email' => 'admin@hospital.com',      'role' => 'admin'],
            ['name' => 'Doctor User',     'email' => 'doctor@hospital.com',     'role' => 'doctor'],
            ['name' => 'Patient User',    'email' => 'patient@hospital.com',    'role' => 'patient'],
            ['name' => 'Pharmacist User', 'email' => 'pharmacist@hospital.com', 'role' => 'pharmacist'],
        ];

        foreach ($users as $u) {
            User::create([
                'name'              => $u['name'],
                'email'             => $u['email'],
                'role'              => $u['role'],
                'password'          => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
