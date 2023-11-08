<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hero;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HeroController extends Controller
{
    public function edit()
    {
        // Fetch the single About Us record (assuming it's the only one)
        $hero = Hero::first();

        return Inertia::render('Admin/EditPage/EditHeroSection', ['hero' => $hero]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'nullable',
            'subtitle' => 'nullable',
            'description' => 'nullable',
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
        $hero = Hero::first();
        $hero->update($data);

        return redirect()->route('hero.edit');
    }
}
