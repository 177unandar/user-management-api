<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        // For each user, create between 5-15 orders
        foreach ($users as $user) {
            $orderCount = rand(5, 15);

            Order::factory()
                ->count($orderCount)
                ->forUser($user)
                ->create();
        }
    }
}
