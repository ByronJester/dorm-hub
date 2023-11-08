<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutUs;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AboutUsController extends Controller
{
    public function show()
    {
        // Fetch the single About Us record (assuming it's the only one)
        $aboutUs = AboutUs::first();
    
        return Inertia::render('AboutUs', ['aboutUs' => $aboutUs]);
    }
public function edit()
{
    // Fetch the single About Us record (assuming it's the only one)
    $aboutUs = AboutUs::first();

    return Inertia::render('Admin/EditPage/EditAboutUs', ['aboutUs' => $aboutUs]);
}

public function update(Request $request)
{
    $request->validate([
        'title' => 'required',
        'sub_title' => 'required',
        'content' => 'required',
        'image_path' => 'nullable',
    ]);

    $data = $request->except(["image_path"]);
    if($request->image_path) {
        $image_path = $request->image_path;
        $filename = Str::random(10) . '_about_us' ;
        $uploadFile = $this->uploadFile($image_path, $filename);
        $data["image_path"] = $filename;
    };

    $data["user_id"] = Auth::user()->id;
    // Update the single About Us record
    $aboutUs = AboutUs::first();
    $aboutUs->update($data);

    return redirect()->route('aboutUs.show')->with('success', 'About Us updated successfully.');
}
}
