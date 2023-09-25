<?php

namespace App\Http\Controllers;

use App\Http\Requests\{ProfileUpdateRequest, IncomeInformationUpdateRequest};
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use App\Models\{UserIncomeInformation};

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function changePassword(Request $request): Response
    {
        return Inertia::render('Profile/ChangePassword', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }


    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if($dp = $request->image) {
            $filename = Str::random(10) . '_dp' ;

            $uploadFile = $this->uploadFile($dp, $filename);
            $data["image"] = $filename;
        }

        $data["bio"] = $request->bio;
        $data["pk"] = $request->pk;
        $data["sk"] = $request->sk;

        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Update the user's income information.
     */
    public function updateIncomeInformation(IncomeInformationUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validated();
        $filename = null;

        if($proof = $request->proof) {
            $filename = Str::random(10) . '_income_proof' ;

            $uploadFile = $this->uploadFile($proof, $filename);
            $data["proof"] = $filename;
        }

        $incomeInfo = UserIncomeInformation::where('user_id', $user->id)->first();

        if(!$incomeInfo) {
            $incomeInfo = new UserIncomeInformation;
        }

        $incomeInfo->user_id = $user->id;
        $incomeInfo->source_of_income = $data['source_of_income'];
        $incomeInfo->monthly_income = $data['monthly_income'];
        $incomeInfo->monthly_expenses = $data['monthly_expenses'];
        $incomeInfo->proof = $filename;

        $incomeInfo->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
