<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GetUsersApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $manager;

    private User $regularUser;

    private string $adminToken;

    private string $managerToken;

    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
            'active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'role' => UserRole::MANAGER,
            'active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->regularUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'role' => UserRole::USER,
            'active' => true,
            'password' => Hash::make('password'),
        ]);

        // Create test orders
        Order::factory()->count(2)->create(['user_id' => $this->admin->id]);
        Order::factory()->count(3)->create(['user_id' => $this->manager->id]);
        Order::factory()->count(1)->create(['user_id' => $this->regularUser->id]);

        // Get tokens for each user type
        $this->adminToken = $this->getToken($this->admin);
        $this->managerToken = $this->getToken($this->manager);
        $this->userToken = $this->getToken($this->regularUser);
    }

    private function getToken(User $user): string
    {
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test Device',
        ]);

        return $response->json('access_token');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_users_list()
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_get_paginated_users_list()
    {
        $tokens = [$this->adminToken, $this->managerToken, $this->userToken];

        foreach ($tokens as $token) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$token,
            ])->getJson('/api/users');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'page',
                    'users' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'role',
                            'created_at',
                            'orders_count',
                            'can_edit',
                        ],
                    ],
                ]);

            $this->assertGreaterThan(0, count($response->json('users')));

            // Verify password is not in the response
            $response->assertJsonMissing(['password']);
        }
    }

    /** @test */
    public function search_users_returns_correct_results()
    {
        // Test search by name
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->getJson('/api/users?search=Admin');

        $response->assertStatus(200);
        $users = $response->json('users');
        $this->assertCount(1, $users);
        $this->assertEquals($this->admin->email, $users[0]['email']);

        // Test search by email
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->getJson('/api/users?search=manager@example.com');

        $response->assertStatus(200);
        $users = $response->json('users');
        $this->assertCount(1, $users);
        $this->assertEquals($this->manager->email, $users[0]['email']);
    }

    /** @test */
    public function users_can_be_sorted()
    {
        // Test sorting by name
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->getJson('/api/users?sortBy=name');

        $response->assertStatus(200);
        $users = $response->json('users');

        if (count($users) >= 2) {
            $this->assertLessThanOrEqual(
                $users[1]['name'],
                $users[0]['name']
            );
        }

        // Test sorting by created_at (default)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->getJson('/api/users?sortBy=created_at');

        $response->assertStatus(200);
        $users = $response->json('users');

        if (count($users) >= 2) {
            $this->assertGreaterThanOrEqual(
                strtotime($users[1]['created_at']),
                strtotime($users[0]['created_at'])
            );
        }
    }

    /** @test */
    public function only_active_users_are_returned()
    {
        // Create an inactive user
        $inactiveUser = User::factory()->create([
            'name' => 'Inactive User',
            'email' => 'inactive@example.com',
            'active' => false,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->adminToken,
        ])->getJson('/api/users');

        $response->assertStatus(200);
        $users = $response->json('users');
        $inactiveUserFound = collect($users)->contains('email', 'inactive@example.com');
        $this->assertFalse($inactiveUserFound, 'Inactive user should not be in the results');
    }
}
