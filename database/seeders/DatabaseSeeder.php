<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with default user accounts only.
     */
    public function run(): void
    {
        // Seed Users for each Role
        $roles = ['admin', 'gudang', 'purchasing', 'produksi', 'owner'];
        foreach ($roles as $role) {
            User::updateOrCreate(
                ['email' => "{$role}@indofroz.com"],
                [
                    'name' => 'Staf ' . ucfirst($role),
                    'password' => Hash::make('password'),
                    'role' => $role,
                ]
            );
        }

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}
