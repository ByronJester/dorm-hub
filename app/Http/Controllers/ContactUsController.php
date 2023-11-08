<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;
use Inertia\Inertia;


class ContactUsController extends Controller
{
    public function show()
    {
        // Fetch the single About Us record (assuming it's the only one)
        $contactUs = ContactUs::first();
    
        return Inertia::render('ContactUs', ['contactUs' => $contactUs]);
    }
    public function edit()
    {
        // Fetch the single About Us record (assuming it's the only one)
        $contactUs = ContactUs::first();

        return Inertia::render('Admin/EditPage/EditContactUs', ['contactUs' => $contactUs]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'phone' => 'required',
            'facebook' => 'required',
            'ig' => 'required',
            'address' => 'required',
        ]);

        // Update the single About Us record
        $contactUs = ContactUs::first();
        $contactUs->update($request->all());

        return redirect()->route('contactUs.show')->with('success', 'Contact Us updated successfully.');
    }
}
