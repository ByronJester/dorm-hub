<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TermsUser;
use Inertia\Inertia;

class TermsUserController extends Controller
{
    public function show()
    {
        // Fetch the single About Us record (assuming it's the only one)
        $terms = TermsUser::first();
    
        return Inertia::render('Components/Terms', ['terms' => $terms]);
    }
    public function edit()
    {
        // Fetch the single About Us record (assuming it's the only one)
        $terms = TermsUser::first();

        return Inertia::render('Admin/EditPage/EditTermsUsers', ['terms' => $terms]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        // Update the single About Us record
        $terms = TermsUser::first();
        $terms->update($request->all());

        return response()->json(["message" => true], 200);
    }
}
