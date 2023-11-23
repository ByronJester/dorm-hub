<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\{ User };

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        $redirect = null;

        if($user->user_type == 'owner') {
            $redirect = RouteServiceProvider::OWNER;
        }

        else if($user->user_type == 'tenant') {
            $redirect = RouteServiceProvider::TENANT;
        }

        else if($user->user_type == 'admin') {
            $redirect = RouteServiceProvider::ADMIN;
        }

        return redirect()->intended($redirect);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // return redirect('/');
        return response()->json("Logged out", 200);
    }
}
