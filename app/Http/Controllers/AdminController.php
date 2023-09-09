<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\{ Dorm };


class AdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Auth/AdminLogin', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function dormList()
    {
        $dorms = Dorm::get();

        return Inertia::render('Admin/Dorms', [
            'dorms' => $dorms,
        ]);
    }

    public function getDormList()
    {
        $dorms = Dorm::get();

        return response()->json(["data" => $dorms], 200);
    }
}
