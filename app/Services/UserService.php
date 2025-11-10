<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Jobs\SendAdminNewUserNotification;
use App\Jobs\SendUserCreatedNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new user with the given data.
     *
     * @throws \Exception
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'] ?? UserRole::USER->value,
            ]);

            $this->dispatchNotifications($user, $data['password']);

            return $user;
        });
    }

    /**
     * Dispatch notification jobs.
     */
    protected function dispatchNotifications(User $user, string $plainPassword): void
    {
        SendUserCreatedNotification::dispatch($user, $plainPassword);
        SendAdminNewUserNotification::dispatch($user);
    }
}
