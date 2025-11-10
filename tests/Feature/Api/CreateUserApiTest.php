<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Jobs\SendAdminNewUserNotification;
use App\Jobs\SendUserCreatedNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateUserApiTest extends TestCase
{
    use RefreshDatabase;

    private string $endpoint = '/api/users';

    public function test_user_can_be_created_with_valid_data()
    {
        Queue::fake();

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson($this->endpoint, $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'email',
                'name',
                'created_at',
            ])
            ->assertJson([
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
            'role' => UserRole::USER->value,
        ]);

        Queue::assertPushed(SendUserCreatedNotification::class);
        Queue::assertPushed(SendAdminNewUserNotification::class);
    }

    public function test_user_creation_requires_validation()
    {
        $response = $this->postJson($this->endpoint, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_email_must_be_unique()
    {
        $existingUser = User::factory()->create();

        $response = $this->postJson($this->endpoint, [
            'name' => 'John Doe',
            'email' => $existingUser->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_password_must_be_confirmed()
    {
        $response = $this->postJson($this->endpoint, [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatched',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
