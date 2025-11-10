@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    # Welcome to {{ config('app.name') }}, {{ $user->name }}!

    Thank you for joining our platform. We're excited to have you on board!

    ### Your Account Details
    **Name:** {{ $user->name }}

    **Email:** {{ $user->email }}

    **Account Created:** {{ $user->created_at->format('F j, Y') }}

    Please verify your email address by clicking the button below to get full access to your account.

    @component('mail::button', ['url' => $verificationUrl])
        Verify Email Address
    @endcomponent

    If you did not create an account, no further action is required.

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        @endcomponent
    @endslot
@endcomponent
