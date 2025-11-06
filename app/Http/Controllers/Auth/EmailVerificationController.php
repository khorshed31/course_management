<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    // Page telling user to verify their email
    public function notice(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }
        return view('auth.verify');
    }

    // Link that the user clicks from email
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill(); // marks email as verified
        return redirect()->intended(route('dashboard'))->with('status', 'Your email is verified.');
    }
}
