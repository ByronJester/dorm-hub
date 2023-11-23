<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{ User, Code };
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
use App\Rules\CodeExists;

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
            'username' => 'required|string|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'code' => ['required', new CodeExists('codes', 'code')]
        ]);



        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'user_type' => $request->user_type,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'first_logged_in' => $request->user_type == 'tenant' ? false : true
        ]);

        event(new Registered($user));

        Code::where('code', $request->code)->delete();

        // Auth::login($user);

        // $redirect = null;

        // if($user->user_type == 'owner') {
        //     $redirect = RouteServiceProvider::OWNER;
        // }

        // if($user->user_type == 'tenant') {
        //     $redirect = RouteServiceProvider::TENANT;
        // }

        // if($user->user_type == 'admin') {
        //     $redirect = RouteServiceProvider::ADMIN;
        // }

        // // return redirect($redirect);
        // 'id_picture' => $id_picture,
        // 'selfie_id_picture' => $selfie_picture,
        // 'is_approved' => false,
        return redirect()->back();
    }
    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'id_picture' => 'required',
            'selfie_id_picture' => 'required',
            'status' => 'required'
        ]);

        $id_picture = Str::random(10) . '_id_picture';
        $selfie_picture = Str::random(10) . '_selfie_picture';

        $this->uploadFile($request->id_picture, $id_picture);
        $this->uploadFile($request->selfie_id_picture, $selfie_picture);

        $data = $request->except(["id_picture", "selfie_id_picture"]);
        if($request->id_picture) {
            $id_picture = $request->id_picture;
            $filename = Str::random(10) . '_id_picture' ;
            $uploadFile = $this->uploadFile($id_picture, $filename);
            $data["id_picture"] = $filename;
        };
        if($request->selfie_id_picture) {
            $selfie_id_picture = $request->selfie_id_picture;
            $filename = Str::random(10) . '_selfie_id_picture' ;
            $uploadFile = $this->uploadFile($selfie_id_picture, $filename);
            $data["selfie_id_picture"] = $filename;
        };

        $user = Auth::user();
        $user->update($data);

        return response()->json(['message' => 'Dorm updated successfully.', 'status' => 200], 200);
    }

}
