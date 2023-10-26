<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    Dorm, Room, Amenity, Rule, Payment, User, Notification,
    Thread, ThreadMember, ThreadMessage, TenantApplication,
    TenantBilling, TenantReservation, TenantPayment
};
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function dormList()
    {
        $dorms = Dorm::where('status', 'approved')->get();

        return Inertia::render('Tenant/Dorms', [
            'dorms' => $dorms,
        ]);
    }

    public function mydorm()
    {
        return Inertia::render('Tenant/MyDorm', [

        ]);
    }


    public function paymentList()
    {
        $auth = Auth::user();

        return Inertia::render('Tenant/Payments', []);
    }

    public function viewBillingInfo($param)
    {
        $auth = Auth::user();

        $routeParam = explode("-", $param);

        $room = Room::where('id', $routeParam[0])->first();
        $dorm = Dorm::where('id', $room->dorm_id)->first();

        $now = Carbon::now();
        $expiredDate = Carbon::now()->addDay(7);

        $hasExistingApplication = TenantApplication::where('tenant_id', $auth->id)
            ->where('room_id', $room->id)->where('status', 'pending')
            ->where('is_active', true)->first();

        return Inertia::render('Tenant/BillingInfo', [
            'room' => $room,
            'dorm' => $dorm,
            'action' => $routeParam[1],
            'now' => $now->isoFormat('LL'),
            'expiredDate' => $expiredDate->isoFormat('LL'),
            'min' => $now,
            'max' => $expiredDate,
            'hasExistingApplication' => !!$hasExistingApplication ? true : false
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
        $data = $request->toArray();

        return TenantApplication::create($data);
    }

    public function submitRoomReservation(Request $request)
    {
        try {
            DB::beginTransaction(); // Begin Transaction

            $auth = Auth::user();

            $now = Carbon::now();

            $applicationRequest = $request->only([
                'owner_id', 'tenant_id', 'dorm_id', 'room_id', 'status', 'check_time'
            ]);

            $applicationRequest['check_date'] = Carbon::parse($request->check_date);

            $application = TenantApplication::create($applicationRequest);

            $reservationRequest = $request->only([
                'owner_id', 'tenant_id', 'dorm_id', 'room_id'
            ]);

            $reservationRequest['date'] = $now;
            $reservationRequest['tenant_application_id'] = $application->id;

            $reservation = TenantReservation::create($reservationRequest);

            $billingRequest = $request->only([
                'tenant_id', 'amount'
            ]);

            $billingRequest['tenant_application_id'] = $application->id;
            $billingRequest['first_name'] = !$request->first_name ? $auth->first_name : $request->first_name;
            $billingRequest['last_name'] = !$request->last_name ? $auth->last_name : $request->last_name;
            $billingRequest['phone_number'] = !$request->phone_number ? $auth->phone_number : $request->phone_number;
            $billingRequest['description'] = 'reservation_fee';
            $billingRequest['date'] = $now;

            $billing = TenantBilling::create($billingRequest);

            $paymentRequest = $request->only([
                'tenant_id', 'dorm_id', 'room_id', 'amount', 'payment_method', 'status'
            ]);

            $paymentRequest['tenant_billing_id'] = $billing->id;
            $paymentRequest['category'] = "reservation_fee";
            $paymentRequest['date'] = $now;
            $paymentRequest['proof_of_payment'] = null;


            if($request->payment_method == "Bank Transfer") {
                $filename = Str::random(10) . '_proof_of_payment';
                $uploadFile = $this->uploadFile($request->proof_of_payment, $filename);
                $paymentRequest['proof_of_payment'] = $filename;
            }

            $payment = TenantPayment::create($paymentRequest);

            DB::commit(); // Commit the transaction

            // Return the data
            return [
                'success' => true,
                'data' => $request->payment_method == "Bank Transfer"
                    ? $payment
                    : $this->payOnline($request->amount, $request->owner_id, $request->tenant_id),
                'payment_method' => $request->payment_method
            ];
        } catch (Exception $e) {
            // Handle exceptions
            DB::rollBack(); // Rollback the transaction
            return ['error' => 'Transaction failed'];
        }

    }

    public function payOnline($amount, $owner_id, $tenant_id)
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

        return $source;

    }

    public function successPage()
    {
        $source_id = session()->get('source_id');
        $source = session()->get('source');
        $owner = session()->get('owner');
        $amount = session()->get('amount');

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

        return Inertia::render('Paymongo/Success', [
            'source' => $source,
            'owner' => $owner,
            'amount' => $amount
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
}
