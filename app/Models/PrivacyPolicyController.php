<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;
use Inertia\Inertia;

class PrivacyPolicyController extends Controller
{
    public function show()
    {
        // Fetch the single About Us record (assuming it's the only one)
        $policy = PrivacyPolicy::first();
    
        return Inertia::render('PrivacyPolicy', ['policy' => $policy]);
    }
    public function edit()
    {
        // Fetch the single About Us record (assuming it's the only one)
        $policy = PrivacyPolicy::first();

        return Inertia::render('Admin/EditPage/EditPrivacyPolicy', ['policy' => $policy]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        // Update the single About Us record
        $policy = PrivacyPolicy::first();
        $policy->update($request->all());

        return redirect()->route('policy.show')->with('success', 'Privacy Policy updated successfully.');
    }
}
