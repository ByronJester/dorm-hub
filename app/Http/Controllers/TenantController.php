<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function dormList()
    {
        return Inertia::render('Tenant/Dorms');
    }
}
