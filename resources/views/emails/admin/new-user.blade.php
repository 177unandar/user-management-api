@component('mail::message')
# New User Registration

A new user has registered on {{ config('app.name') }}.

## User Details
- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}
- **Role:** {{ $user->role->value ?? 'user' }}
- **Registration Date:** {{ $user->created_at->format('F j, Y \a\t H:i') }}

This is an automated notification. No action is required unless you notice any suspicious activity.

Regards,
{{ config('app.name') }} System
@endcomponent
