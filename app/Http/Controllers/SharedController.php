<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\{
    Dorm, Notification, Thread, ThreadMessage, Code , PrivacyPolicy, AboutUs, ContactUs,
    TenantApplication, TenantBilling, TenantPayment, Room, User
};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SharedController extends Controller
{
    public function getlongLat($address)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.tomtom.com/search/2/geocode/$address.json?key={wjvWAT9KJyQfZepSiABAgsa8idqpcLlG}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }

    public function viewDorm($dorm_id)
    {
        $auth = Auth::user();

        $dorm = Dorm::where('id', $dorm_id)->first();

        $application = null;

        if($auth) {
            $application = TenantApplication::where('tenant_id', $auth->id)
                ->where('is_approved', true)->where('is_active', true)->first();
        }


        return Inertia::render('Dorm', [
            'dorm' => $dorm,
            'hasApplication' => !$application ? false : true
        ]);
    }

    public function notificationMarkAsRead($id)
    {
        $auth = Auth::user();

        Notification::where('id', $id)->update(['is_read' => true]);

        $notifications = Notification::orderBy('created_at', 'desc')->where('user_id', $auth->id)->get();

        Inertia::share('notifications', $notifications);

        return redirect()->back();

    }

    public function viewMessages()
    {
        $auth = Auth::user();

        $threads = Thread::with(['messages', 'owner', 'tenant'])->orderBy('created_at', 'desc');

        if($auth->user_type == 'tenant'){
            $threads = $threads->where('tenant_id', $auth->id);
        }

        if($auth->user_type == 'owner'){
            $threads = $threads->where('owner_id', $auth->id);
        }

        return Inertia::render('Messages', [
            'threads' => $threads->get()
        ]);
    }

    public function sendMessage(Request $request)
    {
        $auth = Auth::user();

        ThreadMessage::create([
            'thread_id' => $request->thread_id,
            'message' => $request->message,
            'user_id' => $auth->id
        ]);

        return Thread::with(['messages', 'owner', 'tenant'])->where('id', $request->thread_id)->first();
    }

    public function fetchMessages()
    {
        $auth = Auth::user();

        $threads = Thread::with(['messages', 'owner', 'tenant'])->orderBy('created_at', 'desc');

        if($auth->user_type == 'tenant'){
            $threads = $threads->where('tenant_id', $auth->id);
        }

        if($auth->user_type == 'owner'){
            $threads = $threads->where('owner_id', $auth->id);
        }

        // return response()->json(['threads' => $threads->get()], 200);
        return $threads->get();
    }

    public function sendOTP(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone_number' => 'required|numeric|digits:11',
            'user_type' => 'required',
            'id_picture' => 'required',
            'selfie_id_picture' => 'required',
            'username' => 'required|string|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $length = 8;
        $message = '';

        for ($i = 0; $i < $length; $i++) {
            $message .= rand(0, 9);
        }

        Code::create([
            'code' => $message
        ]);

        $this->sendSMS($request->phone_number, $message);

        return $message;
    }

    public function show()
    {
        // Fetch the single About Us record (assuming it's the only one)
        $aboutUs = AboutUs::first();

        return Inertia::render('AboutUs', ['aboutUs' => $aboutUs]);
    }

    public function showContact()
    {
        $contactUs = ContactUs::first();
        return Inertia::render('ContactUs', [
            'contactUs' => $contactUs
        ]);
    }

    public function showPolicy()
    {
        $policy = PrivacyPolicy::first();
        return Inertia::render('PrivacyPolicy', [
            'policy' => $policy
        ]);
    }

    public function autoBill()
    {
        $applications = TenantApplication::where('auto_bill', true)->get();

        foreach ($applications as $application) {
            $application = (object) $application;

            $room = Room::where('id', $application->room_id)->first();
            $tenant = User::where('id', $application->tenant_id)->first();

            $date1 = Carbon::now();
            $date2 = Carbon::parse($application->move_in);

            if($application->auto_bill_date) {
                $date2 = Carbon::parse($application->auto_bill_date);
            }

            $is30DaysDifference = $date1->diffInDays($date2) == 30;

            if($is30DaysDifference) {
                $application->auto_bill_date = $date1;
                $application->save();

                $billing = TenantBilling::create([
                    'tenant_id' => $tenant->id,
                    'tenant_application_id' => $application->id,
                    'first_name' => $tenant->first_name,
                    'last_name' => $tenant->last_name,
                    'phone_number' => $tenant->phone_number,
                    'amount' => $room->fee,
                    'description' => 'monthly_fee',
                    'date' => $date1
                ]);

                $payments = TenantPayment::create([
                    'tenant_id' => $tenant->id,
                    'tenant_billing_id' => $billing->id,
                    'dorm_id' => $application->dorm_id,
                    'room_id' => $application->room_id,
                    'amount' => $room->fee,
                    'category' => 'monthly_fee',
                    'date' => $date1
                ]);
            }

            return response()->json("Auto Bill!", 200);
        }
    }

    public function dueReminder()
    {
        $billings = TenantBilling::where('is_paid', false)->get();

        $now = Carbon::today();

        foreach ($billings as $billing) {
            $tenant = User::where('id', $billing->tenant_id)->first();
            $billingDate = Carbon::parse($billing->date);
            $amount = $billing->amount;

            $is5DaysBeforeDue = $billingDate->diffInDays($now) == 5;

            if($is5DaysBeforeDue) {
                $messageDate = $billingDate->isoFormat('LL');
                $message = "Your due is on $messageDate with amount of $amount pesos. Login your account https://dormhub.onrender.com";
                $this->sendSMS($tenant->phone_number, $message);
            }

            $isAfterDue = $now->diffInDays($billingDate) == 2;

            $overDue = false;

            if($isAfterDue) {
                $overDue = true;
                $messageDate = $billingDate->isoFormat('LL');
                $message = "Your due on $messageDate is overdue with amount of $amount pesos, please pay the overdue to avoid eviction. Login your account https://dormhub.onrender.com";
                $this->sendSMS($tenant->phone_number, $message);
            }
        }

        return response()->json("Due Reminder!", 200);
    }
}
