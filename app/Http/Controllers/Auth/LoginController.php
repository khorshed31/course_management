<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle login attempt
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function signin(Request $request)
    {
        // Use the default login method of Laravel UI
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Check for the redirect URL after login
            $redirectUrl = $request->input('redirect') ?: route('home');
            return redirect()->to($redirectUrl); // Redirect user to the same page they were on
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    /**
     * Handle redirection after successful login
     */
    public function authenticated(Request $request, $user)
    {
        // Default redirection
        $redirectUrl = $request->input('redirect') ?: route('home');
        return redirect()->to($redirectUrl);
    }
}
