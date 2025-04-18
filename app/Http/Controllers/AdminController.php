<?php

namespace App\Http\Controllers;

use App\DatabaseBackup;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\{BackUp, Dorm, User, Notification, Refund, UserPayment, Tenant, Billing};
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


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

        $users = User::count();
        $dorms = Dorm::count();
        $tenants = Tenant::count();
        $owners = User::where('user_type', 'owner')->count();

        return Inertia::render('Admin/Dashboard',[
            'users' => $users,
            'dorms' => $dorms,
            'tenants' => $tenants,
            'owners' => $owners
        ]);
    }

    public function subscribe() {
        return Inertia::render('Admin/Subscribe',[

        ]);

    }

    public function maintenance() {
        return Inertia::render('Admin/Maintenance',[

        ]);

    }


    public function reports(){
        $dorms = Dorm::get();

        $userArr = [];

        $users = User::get();

        foreach($users as $user) {
            array_push($userArr, [
                'name' => $user->name,
                'username' => $user->username,
                'user_type' => $user->user_type,
                'phone_number' => $user->phone_number,
                'date_registered' => Carbon::parse($user->created_at)->isoFormat('LL')
            ]);
        }

        $incomeArr = [];

        foreach($dorms as $dorm) {
            $tenants = Tenant::where('dorm_id', $dorm->id)->get();

            $netSales = 0;
            foreach($tenants as $tenant) {
                $netSales += Billing::where('profile_id', $tenant->profile_id)->sum('amount');
            }

            array_push($incomeArr, [
                'owner' => $dorm->user->name,
                'phone_number' => $dorm->user->phone_number,
                'dorm_name' => $dorm->property_name,
                'detailed_address' => $dorm->detailed_address,
                'status' => $dorm->status,
                'net_sales' => $netSales
            ]);
        }

        return Inertia::render('Admin/Reports',[
            'dorms' => $dorms,
            'userArr' => $userArr,
            'incomeArr' => $incomeArr
        ]);
    }

    public function refund(){
        $refunds = Refund::where('status', 'ongoing')->get();

        $refundArr = [];

        foreach ($refunds as $refund) {
            $payment = UserPayment::where('id', $refund->user_payment_id)->first();

            if(!!$payment->tenant_id) {
                array_push($refundArr, [
                    'payment_id' => $refund->user_payment_id,
                    'refund_id' => $refund->id,
                    'refund_description' => $payment->description,
                    'payment_method' => $refund->payment_method,
                    'wallet_name' => $refund->wallet_name,
                    'account_number' => $refund->account_number,
                    'account_name' => $refund->account_name,
                    'status' => $refund->status,
                    'refund_date' => Carbon::parse($refund->created_at)->isoFormat('LL'),
                    'proof_of_refund' => $refund->proof_of_refund,
                ]);

            }

        }


        return Inertia::render('Admin/RefundRequest',[
            'refunds' => $refundArr
        ]);
    }

    public function addUser(){
        $users = User::where('user_type', 'admin')->get(['username', 'user_type', 'phone_number', 'created_at']);

        return Inertia::render('Admin/Utilities/AddUser',[
            'users' => $users
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

    public function changeUserStatusApproved(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $user->status = 'approved';
        $user->save();

        $notification = new Notification;

        $notification->user_id = $user->id;
        $notification->message = "Your account has been approved";
        $notification->type = 'Account Status';
        $notification->save();

        return response()->json(["message" => true], 200);
    }

    public function changeUserStatusDecline(Request $request)
    {
        $user = User::where('id', $request->id)->first();

        if (!$user) {
            return response()->json(["message" => "User not found"], 404);
        }
        $reason = $request->input('reason');

        $user->status = 'decline';
        $user->reason = $reason;
        $user->save();

        $notification = new Notification;

        $notification->user_id = $user->id;
        $notification->message = "Your account has been declined. Reason: $reason";
        $notification->type = 'Account Status';
        $notification->save();

        return response()->json(["message" => true], 200);
    }

    public function getDormList()
    {
        $dorms = Dorm::get();

        return response()->json(["data" => $dorms], 200);
    }

    public function changeDormStatusDecline(Request $request)
    {
        $dorm = Dorm::where('id', $request->id)->first();

        if (!$dorm) {
            return response()->json(["message" => "Dorm not found"], 404);
        }
        $reason = $request->input('reason');

        $dorm->status = 'decline';
        $dorm->reason = $reason;
        $dorm->save();

        $notification = new Notification;

        $notification->user_id = $dorm->user_id;
        $notification->message = "Your account has been declined. Reason: $reason";
        $notification->type = 'Dorm Status';
        $notification->redirection = 'owner.dorms';
        $notification->save();

        return response()->json(["message" => "Success"], 200);
    }

    public function changeDormStatusApprove(Request $request)
    {
        $dorm = Dorm::where('id', $request->id)->first();

        if (!$dorm) {
            return response()->json(["message" => "Dorm not found"], 404);
        }

        $dorm->status = 'approved';
        $dorm->save();

        $notification = new Notification;

        $notification->user_id = $dorm->user_id;
        $notification->message = "Your dorm named $dorm->property_name has been approved";
        $notification->type = 'Dorm Status';
        $notification->redirection = 'owner.dorms';
        $notification->save();

        return response()->json(["message" => "Success"], 200);
    }


    public function backUpDatabase(DatabaseBackup $databaseBackup)
    {
        $action = new BackUp;
        $action->user_id = auth()->user()->id;
        $action->operation_type = 'backup';
        $action->operation_date = now();

        sleep(2);
        try {
            $databaseBackup->execute();
        } catch (\Exception $e) {
            return response()->setStatusCode(500);
        }
        $action->save();
        return response()->download(storage_path("backup/db-backup.sql"));
    }

    public function restoreDatabase()
    {

    }

    public function createAdminUser(Request $request)
    {
        $data = $request->toArray();

        $data['password'] = Hash::make('password');
        $data['user_type'] = 'admin';

        return User::create($data);
    }
}
