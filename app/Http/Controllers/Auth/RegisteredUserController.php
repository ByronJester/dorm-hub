<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view of tenant.
     */
    public function tenantRegister(): Response
    {
        return Inertia::render('Auth/TenantRegister');
    }

    /**
     * Display the registration view of owner.
     */
    public function ownerRegister(): Response
    {
        return Inertia::render('Auth/OwnerRegister');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone_number' => 'required|numeric|digits:11',
            'user_type' => 'required',
            'id_picture' => 'required',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $filename = Str::random(10) . '_id_picture' ;

        $uploadFile = $this->uploadFile($request->id_picture, $filename);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'user_type' => $request->user_type,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_picture' => $filename,
            'is_approved' => $request->user_type == 'tenant' ? false : true
        ]);

        event(new Registered($user));

        Auth::login($user);

        $redirect = null;

        if($user->user_type == 'owner') {
            $redirect = RouteServiceProvider::OWNER;
        }

        if($user->user_type == 'tenant') {
            $redirect = RouteServiceProvider::TENANT;
        }

        if($user->user_type == 'admin') {
            $redirect = RouteServiceProvider::ADMIN;
        }

        return redirect($redirect);
    }
}
