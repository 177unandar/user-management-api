<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN->value,
        ]);

        // Create 2 editor users (managers)
        User::factory()->count(2)->sequence(
            [
                'name' => 'Manager One',
                'email' => 'manager1@example.com',
            ],
            [
                'name' => 'Manager Two',
                'email' => 'manager2@example.com',
            ]
        )->create([
            'password' => Hash::make('password'),
            'role' => UserRole::MANAGER->value,
        ]);

        // Create 20 regular users
        User::factory()
            ->count(20)
            ->create([
                'password' => Hash::make('password'),
                'role' => UserRole::USER->value,
            ]);
    }
}
