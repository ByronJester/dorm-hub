<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\{ Dorm, Room, Amenity, Rule, Payment };
use Illuminate\Support\Facades\Validator;


class TenantController extends Controller
{
    public function dormList()
    {
        $dorms = Dorm::where('status', 'approved')->get();

        return Inertia::render('Tenant/Dorms', [
            'dorms' => $dorms,
        ]);
    }

    public function reserveDorm(Request $request)
    {

    }

    public function rentDorm(Request $request)
    {

    }
}
