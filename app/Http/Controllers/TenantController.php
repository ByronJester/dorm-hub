<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    Dorm, Room, Amenity, Rule, Payment, User, Notification,
    Thread, ThreadMember, ThreadMessage,Hero, DormRating, TenantComplaint,
    //TenantApplication, TenantRefund, , TenantReservation, TenantBilling, TenantPayment,
    Reservation, Billing, UserPayment, Application, Tenant, Refund, ContactUs, Profile,
    UserIncomeInformation
};
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Services\XenditService;

class TenantController extends Controller
{
    public function dormList()
    {
        $dorms = Dorm::where('status', 'approved')->get();

        return Inertia::render('Tenant/Dorms', [
            'dorms' => $dorms,
            'hero' => Hero::first(),
        ]);
    }

    public function mydorm($room_id)
    {
        $auth = Auth::user();

        $myApplication = Application::where('tenant_id', $auth->id)->first();

        $myDorm = Tenant::with(['dorm', 'room', 'owner_user', 'tenant_user'])
            ->where('tenant', $auth->id)
            ->where('is_active', true)
            ->where('id', $room_id)
            ->first();

        $rating = DormRating::where('tenant_id', $auth->id)->first();

        $complaints = [];

        if($myDorm) {
            $complaints = TenantComplaint::where('tenant_id', $myDorm->id)->get();
        }

        return Inertia::render('Tenant/MyDorm', [
            'user' => $auth,
            'myApplication' => $myApplication,
            'myDorm' => $myDorm,
            'rating' => $rating,
            'complaints' => $complaints
        ]);
    }

    public function myDormList(){
        $auth = Auth::user();

        
        $applicationStatuses = ['pending', 'declined'];
        $applications = Application::with(['dorm', 'room', 'owner', 'tenant'])
            ->where('tenant_id', $auth->id)->where('is_active', true)->whereIn('status', $applicationStatuses)->get();

        $myDorm = Tenant::with(['dorm', 'room', 'owner_user', 'tenant_user'])
            ->where('tenant', $auth->id)
            ->where('is_active', true)
            ->get();

        return Inertia::render('Tenant/MyDormList', [
            'user' => $auth,
            'myApplication' => $applications,
            'myDorm' => $myDorm,
        ]);
    }

    public function myreservation($room_id)
    {   $auth = Auth::user();

        $myApplication = Application::where('tenant_id', $auth->id)->first();

        $reservation = Reservation::with(['dorm', 'room'])
            ->where('id', $room_id)
            ->where('tenant', $auth->id)
            ->where('is_active', true)
            ->first();

        return Inertia::render('Tenant/MyReservation', [
            'user' => $auth,
            'myApplication' => $myApplication,
            'reservation' => $reservation
        ]);
    }

    public function myReservationList()

    {   $auth = Auth::user();


        $reservation = Reservation::with(['dorm', 'room'])
            ->where('tenant', $auth->id)
            ->where('is_active', true)
            ->get();

        return Inertia::render('Tenant/MyReservationList', [
            'user' => $auth,
            'reservation' => $reservation
        ]);
    }


    public function paymentList()
    {
        $auth = Auth::user();

        $payments = UserPayment::where('user_id', $auth->id)->get();
        $nexPayment = UserPayment::where('is_paid', false)->where('profile_id', $auth->id)->where('description', 'monthly_fee')->first();
        $lastBilled = UserPayment::orderBy('created_at', 'desc')->where('is_paid', false)
            ->whereIn('description', ['monthly_fee', 'advance_and_deposit_fee'])
            ->where('profile_id', $auth->id)
            ->first();

        $paid = UserPayment::where('is_paid', true)->where('profile_id', $auth->id)->get();

        $totalAmountPaid = 0;

        foreach ($paid as $p) {
            $billing = (object) $p->billing;
            $totalAmountPaid += $billing->amount;
        }

        $balance = 0;

        foreach($payments as $payment) {
            if($payment->status == 'pending') {
                $balance += $payment->amount;
            }
        }

        $contact = ContactUs::first();

        $myDorm = Tenant::with(['dorm', 'room', 'owner_user', 'tenant_user'])
            ->where('tenant', $auth->id)
            ->where('is_active', true)
            ->first();

        $profile = Profile::where('user_id', $auth->id)->get();
        $billing = Billing::where('user_id', $auth->id)->get();

        $nextPayments = Billing::where('is_paid', false)
            ->where('user_id', $auth->id)
            ->where('description', 'Monthly Fee')
            ->get();

        $balances = Billing::where('is_paid', false)
            ->where('user_id', $auth->id)
            ->get();

        $amountPaids = Billing::where('is_paid', true)
            ->where('user_id', $auth->id)
            ->get();



        return Inertia::render('Tenant/Payments', [
            'contact' => $contact,
            'myDorm' => $myDorm,
            'payments' => $payments,
            'nexPayment' => $nexPayment,
            'lastBilled' => $lastBilled,
            'balance' => $balance,
            'totalAmountPaid' => $totalAmountPaid,
            'profile'=>$profile,
            'bills'=>$billing,
            'nextPayments' => $nextPayments,
            'balances' => $balances,
            'amountPaids' => $amountPaids
        ]);
    }

    public function viewBillingInfo($param)
    {
        $auth = Auth::user();

        $routeParam = explode("-", $param);

        // return $routeParam[0];

        $room = Room::where('id', $routeParam[0])->first();
        $dorm = Dorm::where('id', $room->dorm_id)->first();
        $profile = Profile::where('user_id', $auth->id)
            ->whereDoesntHave('applications')
            ->get();

        $now = Carbon::now();
        $expiredDate = Carbon::now()->addDay(7);

        // $hasExistingApplication = TenantApplication::where('tenant_id', $auth->id)
        //     ->where('room_id', $room->id)->where('status', 'rent')
        //     ->where('is_active', true)->first();

        // $hasExistingReservation = TenantApplication::where('tenant_id', $auth->id)
        //     ->where('room_id', $room->id)->where('status', 'reserve')
        //     ->where('is_active', true)->first();

        return Inertia::render('Tenant/BillingInfo', [
            'profile' => $profile,
            'room' => $room,
            'dorm' => $dorm,
            'action' => $routeParam[1],
            'now' => $now->isoFormat('LL'),
            'expiredDate' => $expiredDate->isoFormat('LL'),
            'min' => $now,
            'max' => $expiredDate,
            'hasExistingApplication' => false,
            'hasExistingReservation' => false
        ]);
    }

    public function reserveRoom(Request $request)
    {
        $user = Auth::user();

        $tenantRoom = new TenantRoom;

        $tenantRoom->owner_id = $request->owner_id;
        $tenantRoom->tenant_id = $request->tenant_id;
        $tenantRoom->dorm_id = $request->dorm_id;
        $tenantRoom->room_id = $request->room_id;
        $tenantRoom->status = $request->status;
        $tenantRoom->from_reserve = true;

        $notification = new Notification;

        $notification->user_id = $tenantRoom->owner_id;
        $notification->message = "You received a rental reservation from $user->name";
        $notification->type = 'Rental Application.';
        $notification->redirection = 'owner.tenants.application';
        $notification->save();

        $tenantRoom->save();

        Room::where('id', $request->room_id)->update(['is_available' => false]);

    }

    public function rentRoom(Request $request)
    {
        $user = Auth::user();

        $tenantRoom = new TenantRoom;

        $tenantRoom->owner_id = $request->owner_id;
        $tenantRoom->tenant_id = $request->tenant_id;
        $tenantRoom->dorm_id = $request->dorm_id;
        $tenantRoom->room_id = $request->room_id;
        $tenantRoom->status = $request->status;

        $notification = new Notification;

        $notification->user_id = $tenantRoom->owner_id;
        $notification->message = "You received rental application from $user->name";
        $notification->type = 'Rental Application.';
        $notification->redirection = 'owner.tenants.application';
        $notification->save();

        $tenantRoom->save();

        Room::where('id', $request->room_id)->update(['is_available' => false]);
    }

    public function payRent($id, Request $request)
    {
        if($request->method == 'Bank Payment') {
            return self::payViaBank($id, $request);
        }

        if($request->method == 'GCash Payment') {
            return self::payViaGcash($id);
        }

    }

    public function payViaBank($id, $request)
    {
        $auth = Auth::user();
        $payment = TenantPayments::where('id', $id)->first();
        $application = TenantRoom::where('id', $payment->tenant_room_id)->first();
        $owner = User::where('id', $application->owner_id)->first();

        $payment->mode_of_payment = 'Bank';

        $filename = null;

        if($request->has('receipt')) {
            $receipt = $request->receipt;

            $filename = Str::random(10) . '_bank_receipt' ;

            $uploadFile = $this->uploadFile($receipt, $filename);
        }

        $amount = 0;

        if($payment->partial) {
            $payment->pending_payment = $payment->partial;
            $amount = $payment->partial;
            $payment->partial_receipt = $filename;
            $application->expired_at = Carbon::now()->addDay(7);
        } else {
            $payment->pending_payment = $payment->amount_to_pay;
            $amount = $payment->amount_to_pay;

            if($payment->amount_paid != null && $payment->amount_to_pay != $payment->amount_paid) {
                $payment->pending_payment = $payment->amount_to_pay - $payment->amount_paid;
                $amount = $payment->amount_to_pay - $payment->amount_paid;
            }

            $payment->receipt = $filename;
        }

        $notification = new Notification;
        $notification->user_id = $owner->id;
        $notification->message = "You received Bank Payment of ₱$amount";
        $notification->type = 'Payment';

        $notification->save();
        $payment->save();
        $application->save();


        return response()->json('Bank Payment', 200);
    }

    public function payViaGcash($id)
    {
        $auth = Auth::user();
        $payment = TenantPayments::where('id', $id)->first();
        $application = TenantRoom::where('id', $payment->tenant_room_id)->first();
        $owner = User::where('id', $application->owner_id)->first();

        $amount_paid = 0;
        $expired_at = null;
        $partial = $payment->partial;
        $is_paid = false;

        if(!!$payment->partial) {
            $amount_paid = $payment->partial;
            $partial = null;

            if($application->status == 'reserve') {
                $expired_at = Carbon::now()->addDay(7);
            }
        } else {
            if($payment->amount_paid != null && $payment->amount_paid != $payment->amount_to_pay) {
                $amount_paid = $payment->amount_to_pay - $payment->amount_paid;
            }

            if($payment->amount_paid == null) {
                $amount_paid = $payment->amount_to_pay;
            }

            $is_paid = true;
        }

        $body = [
            "data" => [
                "attributes" => [
                    "amount" => (int) $amount_paid * 100,
                    "type" => 'gcash',
                    "currency" => "PHP",
                    "redirect" => [
                        "success" => url('tenant/paymongo/success'),
                        "failed" => url('tenant/paymongo/failed')
                    ],
                    "billing" => [
                        "name" => $auth->name,
                        "phone" => $auth->phone_number,
                        "email" => 'balogz2203@gmail.com'
                    ]
                ]
            ]
        ];

        $client = new \GuzzleHttp\Client(['verify' => false]);

        $response = $client->request('POST', 'https://api.paymongo.com/v1/sources', [
            'body' => json_encode($body),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Basic " . base64_encode($owner->pk),
                'Content-Type' => 'application/json',
            ],
        ]);


        $body = json_decode($response->getBody());
        $source = $body->data->attributes;
        $url = $source->redirect->checkout_url;

        session()->put('source_id', $body->data->id);
        session()->put('source', $source);
        session()->put('owner', $owner);
        session()->put('payment',
            (object) [
                'id' => $id,
                'amount_paid' => $amount_paid,
                'mode_of_payment' => 'GCash',
                'partial' => $partial,
                'is_paid' => $is_paid
            ]
        );
        session()->put('application',
            (object) [
                'id' => $application->id,
                'expired_at' => $expired_at
            ]
        );

        return $source;
    }

    public function tenantverif(){
        return Inertia::render('Tenant/TenantVerif');
    }

    public function messageOwner($owner_id)
    {
        $auth = Auth::user();

        $tread = Thread::updateOrCreate(
            ['tenant_id' => $auth->id, 'owner_id' => $owner_id],
            ['tenant_id' => $auth->id, 'owner_id' => $owner_id]
        );

        return redirect()->route('view.user.messages');
    }

    public function submitApplication(Request $request)
    {
        $auth = Auth::user();

        Room::where('id', $request->room_id)->update([
            'status' => 'rent',
            'is_available' => false
        ]);

        $reservation = Reservation::where('room_id', $request->room_id)->where('tenant', $auth->id)
            ->first();

        if($reservation) {
            $reservation->is_active = false;
        }

        $incomeInfo = new UserIncomeInformation;
        $incomeInfo->profile_id = $request->profile_id;
        $incomeInfo->source_of_income = $request->source_of_income;
        $incomeInfo->monthly_income = $request->monthly_income;

        if($proof = $request->proof_of_income) {

            $filename = Str::random(10) . '_proof';

            $uploadFile = $this->uploadFile($proof, $filename);

            $incomeInfo->proof = $filename;
        }

        $incomeInfo->save();

        return Application::updateOrCreate(
            [
                "owner_id" => $request->owner_id,
                "tenant_id" => $request->tenant_id,
                "room_id" => $request->room_id,
                "dorm_id" => $request->dorm_id,
                "profile_id" => $request->profile_id,
                "is_active" => true,
            ],
            [
                "owner_id" => $request->owner_id,
                "tenant_id" => $request->tenant_id,
                "room_id" => $request->room_id,
                "dorm_id" => $request->dorm_id,
                "is_active" => true,
                "move_in" => Carbon::parse($request->move_in),
                "profile_id" => $request->profile_id,
                "status" => "pending"
            ],
        );
    }

    public function submitRoomReservation(Request $request)
    {
        try {
            DB::beginTransaction(); // Begin Transaction

            $auth = Auth::user();

            $owner = User::where('id', $request->owner_id)->first();

            $amount = $request->reservation_fee;
            $description = 'Reservation Fee';
            $action = 'tenantPayment';

            $xenditService = new XenditService($owner->sk);

            $response = $xenditService->create($amount, $description, $action);

            if(!!$response) {
                $profile = Profile::where('user_id', $auth->id)->where('relationship', 'Me')->first();

                $reservation = Reservation::create([
                    'owner' => $request->owner_id,
                    'tenant' => $request->tenant_id,
                    'dorm_id' => $request->dorm_id,
                    'room_id' => $request->room_id,
                    'check_date' => Carbon::parse($request->check_date),
                    'check_time' => $request->check_time,
                    'date' => Carbon::now(),
                    'expired_at' => Carbon::now()->addDays(7),
                    'is_active' => false
                ]);

                $billing = Billing::create([
                    'f_id' => $reservation->id,
                    'profile_id' => $profile->id,
                    'user_id' => $auth->id,
                    'amount' => $amount,
                    'description' => $description,
                    'type' => 'reservation',
                    'invoice_number' => $response['external_id'],
                    'is_paid' => false,
                    'payment_date' => null,
                    'for_the_month' => null,
                    'is_active' => false
                ]);

                DB::commit();

                return $response['invoice_url'];
            }

            return null;


        } catch (Exception $e) {
            // Handle exceptions
            DB::rollBack(); // Rollback the transaction
            return ['error' => 'Transaction failed'];
        }

    }

    public function payBilling(Request $request)
    {
        $billing = Billing::where('id', $request->billing_id)->first();
        $tenant = Tenant::where('id', $billing->tenant_id)->first();
        $payment = UserPayment::where('id', $request->id)->first();

        $method = $request->payment_method;
        $amount = $request->amount;

        if($method == 'Online Payment') {
            return [
                "data" => $this->payOnline($amount, $tenant->owner, $tenant->tenant, 'Deposit and Advance Fee', $request->id)
            ];
        } else {
            $filename = Str::random(10) . '_proof_of_payment';
            $uploadFile = $this->uploadFile($request->proof_of_payment, $filename);

            $payment->status = "waiting_for_approval";
            $payment->proof_of_payment = $filename;
            $payment->payment_method = $method;
            $payment->save();

            return [
                "data" => $payment
            ];

        }

        return [
            "data" => null
        ];
    }

    public function payOnline($amount, $owner_id, $tenant_id, $description, $payment_id = null)
    {
        $tenant = User::where('id', $tenant_id)->first();
        $owner = User::where('id', $owner_id)->first();

        $body = [
            "data" => [
                "attributes" => [
                    "amount" => (int) $amount * 100,
                    "type" => 'gcash',
                    "currency" => "PHP",
                    "redirect" => [
                        "success" => url('tenant/paymongo/success'),
                        "failed" => url('tenant/paymongo/failed')
                    ],
                    "billing" => [
                        "name" => $tenant->name,
                        "phone" => $tenant->phone_number,
                        "email" => 'balogz2203@gmail.com'
                    ]
                ]
            ]
        ];

        $client = new \GuzzleHttp\Client(['verify' => false]);

        $response = $client->request('POST', 'https://api.paymongo.com/v1/sources', [
            'body' => json_encode($body),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Basic " . base64_encode($owner->pk),
                'Content-Type' => 'application/json',
            ],
        ]);


        $body = json_decode($response->getBody());
        $source = $body->data->attributes;
        $url = $source->redirect->checkout_url;

        session()->put('source_id', $body->data->id);
        session()->put('source', $source);
        session()->put('owner', $owner);
        session()->put('amount', $amount);
        session()->put('payment_id', $payment_id);
        session()->put('tenant', $tenant);
        session()->put('description', $description);

        return $source;

    }

    public function successPage()
    {
        $source_id = session()->get('source_id');
        $source = session()->get('source');
        $owner = session()->get('owner');
        $amount = session()->get('amount');
        $payment_id = session()->get('payment_id');
        $tenant = session()->get('tenant');
        $description = session()->get('description');

        $client = new \GuzzleHttp\Client(['verify' => false]);

        $response = $client->request('POST', 'https://api.paymongo.com/v1/payments', [
            'body' => '{"data":{"attributes":{"amount":' . ($amount * 100) . ',"source":{"id":"' . $source_id . '","type":"source"},"currency":"PHP","description":"GCash Payment"}}}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Basic " . base64_encode($owner->sk),
                'content-type' => 'application/json',
            ],
        ]);

        $notification = new Notification;
        $notification->user_id = $owner->id;
        $notification->message = "You received GCash Payment of ₱$amount";
        $notification->type = 'Payment';
        $notification->save();

        if($payment_id) {
            $payment = UserPayment::where('id', $payment_id)->first();
            $billing = Billing::where('id', $payment->billing_id)->first();

            $payment->status = 'paid';
            $payment->payment_method = "Online Payment";
            $payment->save();

            $billing->is_paid = true;
            $billing->save();
        }

        $invoice = $this->generateInvoice($tenant->id);

        if (session()->has('room_id')) {
            $room_id = session()->get('room_id');

            $room = Room::where('id', $room_id)->first();
            $room->is_available = false;
            $room->status = 'reserve';
            $room->save();

            session()->forget('room_id');

        }

        return Inertia::render('Paymongo/Success', [
            'source' => $source,
            'owner' => $owner,
            'amount' => $amount,
            'invoice' => $invoice,
            'tenant' => $tenant,
            'description' => $description
        ]);
    }

    public function failedPage()
    {
        $source_id = session()->get('source_id');
        $source = session()->get('source');
        $owner = session()->get('owner');
        $amount = session()->get('amount');

        return Inertia::render('Paymongo/Fail', [
            'source' => $source,
            'owner' => $owner,
            'amount' => $amount
        ]);
    }

    public function rateDorm(Request $request)
    {
        $auth = Auth::user();

        $tenant = Tenant::where('tenant', $auth->id)
            ->where('is_active', true)->first();

        return DormRating::updateOrCreate(
            ['tenant_id' => $auth->id],
            [
                'dorm_id' => $tenant->dorm_id,
                'tenant_id' => $auth->id,
                'rate' => $request->rating,
                'comment' => $request->comment
            ]
        );
    }

    public function submitComplain(Request $request)
    {
        $auth = Auth::user();

        $tenant = Tenant::where('tenant', $auth->id)
            ->where('is_active', true)->first();

        return TenantComplaint::create([
            'tenant_id' => $tenant->id,
            'subject' => $request->subject,
            'complain' => $request->complain
        ]);
    }

    public function tenantMoveOut(Request $request)
    {
        $auth = Auth::user();

        $tenant = Tenant::where('tenant', $auth->id)
            ->where('is_active', true)->first();

        $billing = Billing::where('description', 'advance_and_deposit_fee')
            ->where('tenant_id', $tenant->id)->first();

        $payment = UserPayment::where('billing_id', $billing->id)->first();

        $room = (object) $tenant->room;
        $tenant->move_out = Carbon::parse($request->move_out);
        $tenant->reason = $request->reason;
        $tenant->reason_description = $request->reason_description;
        $tenant->status = 'pending_move_out';

        Refund::create([
            'user_payment_id' => $payment->id,
            'amount' => $room->deposit,
            'payment_method' => $request->payment_method,
            'wallet_name' => $request->wallet_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ]);

        return $tenant->save();
    }

    public function viewReceipt()
    {
        // $auth = Auth::user();

        // $invoice = $this->generateInvoice($auth->id);

        // return Inertia::render('Paymongo/Success', [
        //     'owner' => $owner,
        //     'amount' => $amount,
        //     'invoice' => $invoice,
        //     'tenant' => $tenant,
        //     'description' => $description
        // ]);
    }

    public function cancelReservation(Request $request)
    {
        $reservation = Reservation::where('id', $request->reservation_id)->first();
        $reservation->is_active = false;

        $room = Room::where('id', $reservation->room_id)->first();
        $room->is_available = true;
        $room->status = null;

        return $reservation->save() && $room->save();
    }

    public function rentNow(Request $request)
    {
        Reservation::where('id', $request->reservation_id)->update([
            'is_active' => false
        ]);

        Room::where('id', $request->room_id)->update([
            'status' => 'rent'
        ]);

        return Application::updateOrCreate(
            [
                "owner_id" => $request->owner_id,
                "tenant_id" => $request->tenant_id,
                "room_id" => $request->room_id,
                "dorm_id" => $request->dorm_id,
                "is_active" => true,
            ],
            [
                "owner_id" => $request->owner_id,
                "tenant_id" => $request->tenant_id,
                "room_id" => $request->room_id,
                "dorm_id" => $request->dorm_id,
                "is_active" => true,
                "move_in" => Carbon::parse($request->move_in),
                "status" => "pending"
            ],
        );
    }

    public function requestRefund(Request $request)
    {
        UserPayment::where('id', $request->user_payment_id)->update([
            'status' => 'pending_refund'
        ]);

        return Refund::create($request->all());
    }

    public function createSubProfile(Request $request)
    {
        $auth = Auth::user();

        $data = $request->toArray();

        $data['user_id'] = $auth->id;

        Profile::create($data);

        return response()->json(["success" => true], 200);
    }

    public function payBill(Request $request)
    {
        $data = null;
        $type = $request->type;

        if($type == 'rent') {
            $data = Tenant::where('id', $request->f_id)->first();
        } else {
            $data = Reservation::where('id', $request->f_id)->first();
        }

        $owner = User::where('id', $data->owner)->first();

        $amount = $request->amount;
        $description = $request->description;
        $action = 'tenantPayment';

        $xenditService = new XenditService($owner->sk);

        $response = $xenditService->create($amount, $description, $action);

        if(!!$response) {
            $billing = Billing::where('id', $request->id)->first();
            $billing->invoice_number = $response['external_id'];
            $billing->save();

            return $response['invoice_url'];
        }

        return null;
    }

    public function tenantPaymentSuccessPage($invoice)
    {
        $billing = Billing::where('invoice_number', $invoice)->first();

        $xxx = null;

        $response = [];

        $owner = null;

        if($billing) {
            $sk = null;

            if($billing->type == 'reservation') {
                $reservation = Reservation::where('id', $billing->f_id)->first();

                if($reservation) {
                    $xxx = $reservation;
                    $xxx->is_active = true;

                    Room::where('id', $reservation->room_id)->update([
                        'status' => 'reserve',
                        'is_available' => false
                    ]);

                    $owner = User::where('id', $reservation->owner)->first();

                }
            } else {
                $application = Application::where('id', $billing->f_id)->first();

                if($application) {
                    $xxx = $application;
                    $xxx->is_active = true;

                    Room::where('id', $application->room_id)->update([
                        'status' => 'reserve',
                        'is_available' => false
                    ]);

                    $owner = User::where('id', $application->owner_id)->first();
                }
            }

            $billing->is_paid = true;
            $billing->payment_date = Carbon::now();
            $billing->is_active = true;

            // return $owner;

            if($owner->sk) {

                $xenditService = new XenditService($owner->sk);

                $response = $xenditService->get($invoice);

                $billing->save();

                $xxx->save();

                $existingPayment = UserPayment::where('invoice_number', $invoice)->first();

                if(!$existingPayment) {
                    UserPayment::create([
                        'f_id' => $billing->f_id,
                        'user_id' => $billing->user_id,
                        'profile_id' => $billing->profile_id,
                        'amount' => $billing->amount,
                        'description' => $billing->description,
                        'type' => $billing->description,
                        'invoice_number' => $invoice,
                        'is_paid' => true,
                        'payment_date' => Carbon::now(),
                        'for_the_month' => $billing->for_the_month,
                        'is_active' => true,
                        'payment_method' => $response['data'][0]['channel_code']
                    ]);
                }
            }
        }

        return Inertia::render('Xendit/SuccessTenant', [
            'billing' => $billing,
            'invoice' => count($response['data'])  > 0 ? $response['data'][0] : null
        ]);

    }
}

