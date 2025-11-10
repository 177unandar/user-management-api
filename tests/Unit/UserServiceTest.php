<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Jobs\SendAdminNewUserNotification;
use App\Jobs\SendUserCreatedNotification;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    private array $userData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = new UserService;

        $this->userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'role' => UserRole::USER->value,
        ];

        // Mock the queue facade to prevent actual job dispatching
        Queue::fake();
    }

    public function test_it_creates_user_successfully()
    {
        $user = $this->userService->createUser($this->userData);

        $this->assertDatabaseHas('users', [
            'name' => $this->userData['name'],
            'email' => $this->userData['email'],
            'role' => $this->userData['role'],
        ]);

        $this->assertNotEquals($this->userData['password'], $user->password);
        $this->assertTrue(Hash::check($this->userData['password'], $user->password));

        // Assert the jobs were dispatched
        Queue::assertPushed(SendUserCreatedNotification::class);
        Queue::assertPushed(SendAdminNewUserNotification::class);
    }

    public function test_it_creates_user_with_default_role()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $user = $this->userService->createUser($data);

        $this->assertEquals(UserRole::USER->value, $user->role->value);
    }
}
