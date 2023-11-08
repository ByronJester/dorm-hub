<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\{ Dorm, User, Notification };


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

    public function dashboard() {
        return Inertia::render('Admin/Dashboard',[

        ]);
    }

    public function maintenance() {
        return Inertia::render('Admin/Maintenance',[

        ]);

    }


    public function reports(){
        $dorms = Dorm::get();

        return Inertia::render('Admin/Reports',[
            'dorms' => $dorms,
        ]);
    }

    public function addUser(){
        return Inertia::render('Admin/Utilities/AddUser',[

        ]);
    }

    public function archive(){
        return Inertia::render('Admin/Utilities/Archive',[

        ]);
    }

    public function backUp(){
        return Inertia::render('Admin/Utilities/BackUpAndRestore',[

        ]);
    }

    public function tenantList()
    {
        $users = User::whereIn('user_type', ['tenant', 'owner'])->get();

        return Inertia::render('Admin/Users', [
            'users' => $users,
        ]);
    }

    public function changeTenantStatus(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $user->is_approved = $request->status;
        $user->save();

        $notification = new Notification;

        $status = $request->status ? 'approved' : 'declined';
        $message = $status == 'approved' ? 'Your account has been approved you can logged in your account now.' : 'Your account has been declined';

        if($status == 'approved') {
            $notification->redirection = 'tenant.dorms';

            $this->sendSMS($user->phone_number, $message);
        }


        $notification->user_id = $user->id;
        $notification->message = "Your account has been $status";
        $notification->type = 'Account Status';
        $notification->save();

        return response()->json(["message" => true], 200);
    }

    public function getDormList()
    {
        $dorms = Dorm::get();

        return response()->json(["data" => $dorms], 200);
    }

    public function changeDormStatus(Request $request, $status)
    {
        // Dorm::where('id', $request->id)->update(['status', $status]);
        $dorm = Dorm::where('id', $request->id)->first();
        $dorm->status = $status;
        $dorm->save();

        $notification = new Notification;

        $notification->user_id = $dorm->user_id;
        $notification->message = "Your dorm named $dorm->property_name has been $status";
        $notification->type = 'Dorm Status';
        $notification->redirection = 'owner.dorms';
        $notification->save();

        return response()->json(["message" => "Success"], 200);
    }
}
