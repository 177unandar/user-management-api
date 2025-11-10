<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class VerificationSuccessController extends Controller
{
    /**
     * Show the email verification success page.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.verify-success');
    }
}
