<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    public function dormList()
    {
        return Inertia::render('Owner/Dorms');
    }

    public function saveDorm(Request $request)
    {
        return $request->rooms;
    }
}
