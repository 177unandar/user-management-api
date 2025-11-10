<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Jobs\SendAdminNewUserNotification;
use App\Jobs\SendUserCreatedNotification;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Get paginated users with search and sort
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()->active()->withCount('orders');

        // Apply search filter if provided
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $filters['sortBy'] ?? 'created_at';
        $sortDirection = str_starts_with($sortBy, '-') ? 'desc' : 'asc';
        $sortBy = ltrim($sortBy, '-');

        $query->orderBy($sortBy, $sortDirection);

        // Get paginated results
        $perPage = min(100, $filters['per_page'] ?? $perPage); // Prevent too large page sizes
        $page = $filters['page'] ?? 1;

        return $query->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );
    }

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
