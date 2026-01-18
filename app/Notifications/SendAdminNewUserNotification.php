<?php

namespace App\Notifications;

use App\Mail\AdminUserNotification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAdminNewUserNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function handle(): void
    {
        // Get admin emails (you might want to get this from config or database)
        $adminEmails = config('app.admin_emails', ['admin@example.com']);

        foreach ($adminEmails as $email) {
            Mail::to($email)
                ->send(new AdminUserNotification($this->user));
        }
    }
}
