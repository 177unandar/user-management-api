<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verify(Request $request)
    {
        $user = User::find($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Invalid verification link'], 403)
                : redirect(config('app.url').'/email/verify/invalid');
        }

        if ($user->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Email already verified'])
                : redirect(config('app.url').'/email/verify/success');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            $user->update(['active' => true]);
        }

        return $request->wantsJson()
            ? response()->json(['message' => 'Email verified successfully'])
            : redirect(config('app.url').'/email/verify/success');
    }

    /**
     * Resend the email verification notification.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Email already verified'])
                : back()->with('verified', true);
        }

        $request->user()->sendEmailVerificationNotification();

        return $request->wantsJson()
            ? response()->json(['message' => 'Verification email resent'])
            : back()->with('resent', true);
    }
}
