<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\{
    Dorm, Notification, Thread, ThreadMessage, Code ,
    PrivacyPolicy, AboutUs, ContactUs,Room, User,
    // TenantApplication, TenantBilling, TenantPayment,
    Reservation, Application, Billing, UserPayment, Tenant,
    Help
};
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Rules\CodeExists;
use Illuminate\Validation\Rules;

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

        $reservation = null;
        $application = null;

        if($auth) {
            $reservation = Reservation::where('tenant', $auth->id)->where('is_active', true)->first();
            $application = Application::where('tenant_id', $auth->id)->where('is_active', true)->first();
        }

        return Inertia::render('Dorm', [
            'dorm' => $dorm,
            'notAllowedToRentReserve' => $reservation || $application ? true : false
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

    public function help()
    {
        $help = Help::all();

        return Inertia::render('Help', [
            'help' => $help
        ]);
    }

    public function autoBill()
    {
        $tenants = Tenant::where('auto_bill', true)->get();

        foreach ($tenants as $tenant) {
            $tenant = (object) $tenant;

            $room = Room::where('id', $tenant->room_id)->first();
            $tenantUser = User::where('id', $tenant->tenant)->first();

            $date1 = Carbon::now();
            $date2 = Carbon::parse($tenant->auto_bill_date);

            $is30DaysDifference = $date1->diffInDays($date2) == 0;

            if($is30DaysDifference) {
                $tenant->auto_bill_date = Carbon::now()->addMonthsNoOverflow(1);
                $tenant->save();

                $billing = Billing::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $tenant->tenant,
                    'amount' => $room->fee,
                    'description' => 'monthly_fee',
                    'date' => $date1
                ]);

                $payments = UserPayment::create([
                    'tenant_id' => $tenant->id,
                    'billing_id' => $billing->id,
                    'user_id' => $tenant->tenant,
                    'amount' => $room->fee,
                    'description' => 'monthly_fee',
                    'date' => $date1
                ]);
            }

            return response()->json("Auto Bill!", 200);
        }
    }

    public function dueReminder()
    {
        $billings = Billing::where('is_paid', false)->get();

        $now = Carbon::today();

        foreach ($billings as $billing) {
            $tenant = User::where('id', $billing->user_id)->first();
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

    public function reservationExpiration()
    {
        $reservations = Reservation::where('is_active', true)->get();
        $now = Carbon::now();

        foreach($reservations as $reservation) {
            $expirationDate = Carbon::parse($reservation->expired_at);
            $room = Room::where('id', $reservation->room_id)->first();

            if ($now->isSameDay($expirationDate)) {
                $reservation->is_active = false;
                $reservation->save();

                $room->status = null;
                $room->is_available = true;
                $room->save();
            }
        }

        return response()->json("Resrvation Expiration", 200);
    }
}
