<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Mail\AdminUserNotification;
use App\Mail\UserCreatedNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_notification_to_new_user()
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Send the notification
        $mailable = new UserCreatedNotification($user);
        Mail::to($user->email)->send($mailable);

        // Assert a mailable was sent to the given user
        Mail::assertSent(UserCreatedNotification::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) &&
                   $mail->user->is($user);
        });
    }

    /** @test */
    public function it_sends_notification_to_admin_when_new_user_registers()
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'email' => 'admin@example.com',
        ]);

        $newUser = User::factory()->create([
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);

        // Send the admin notification
        $mailable = new AdminUserNotification($newUser);
        Mail::to($admin->email)->send($mailable);

        // Assert the admin notification was sent
        Mail::assertSent(AdminUserNotification::class, function ($mail) use ($admin, $newUser) {
            return $mail->hasTo($admin->email) &&
                   $mail->user->is($newUser);
        });
    }
}
