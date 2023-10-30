<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FirstTimeLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->user_type == 'owner' && auth()->user()->first_logged_in) {
            auth()->user()->update(['first_login' => false]); // Update the flag
            return redirect()->route('owner.dorms'); // Redirect to the first-time login route
        }

        return $next($request);
    }
}
