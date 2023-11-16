<?php

namespace App\Http\Controllers;

use App\DatabaseBackup;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\{BackUp, Dorm, User, Notification, Refund, UserPayment, Tenant};
use Carbon\Carbon;


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
}
