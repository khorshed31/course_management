<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.passwords.forgot-password');
    }
    public function showLinkRequestForm()
    {
        return $this->create(); // alias to new method
    }
    public function sendResetLinkEmail(Request $request)
    {
        // Legacy alias → new method
        return $this->store($request);
    }

    public function store(Request $request)
    {
        $request->validate(['email' => ['required','email']]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
