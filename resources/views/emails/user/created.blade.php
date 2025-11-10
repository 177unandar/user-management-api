@component('mail::message')
# Welcome to Our Application, {{ $user->name }}!

Thank you for joining our platform. We're excited to have you on board!

## Your Account Details
- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Account Created:** {{ $user->created_at->format('F j, Y') }}

@component('mail::button', ['url' => url('/login')])
Login to Your Account
@endcomponent

If you have any questions, feel free to contact our support team.

Thanks,
{{ config('app.name') }}
@endcomponent
