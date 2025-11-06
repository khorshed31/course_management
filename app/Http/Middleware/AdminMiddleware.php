<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check authentication first
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Assuming your users table has a 'role' column
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Only admin can access this page.');
        }

        return $next($request);
    }
}
