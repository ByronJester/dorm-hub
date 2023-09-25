<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\{ Dorm, Room, Amenity, Rule, Payment, TenantRoom, TenantPayments, User, Notification };
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class TenantController extends Controller
{
    public function dormList()
    {
        $dorms = Dorm::where('status', 'approved')->get();

        return Inertia::render('Tenant/Dorms', [
            'dorms' => $dorms,
        ]);
    }

    public function paymentList()
    {
        $auth = Auth::user();

        $application = TenantRoom::where('tenant_id', $auth->id)->where('is_active', true)->first();
        $payments = TenantPayments::where('user_id', $auth->id)->where('is_paid', false)->get();

        $owner = null;
        if($application) {
            $owner = User::where('id', $application->owner_id)->first();
        }

        return Inertia::render('Tenant/Payments', [
            'applicaiton' => $application,
            'payments' => $payments,
            'owner' => $owner
        ]);
    }

    public function reserveRoom(Request $request)
    {
        $tenantRoom = new TenantRoom;

        $tenantRoom->owner_id = $request->owner_id;
        $tenantRoom->tenant_id = $request->tenant_id;
        $tenantRoom->dorm_id = $request->dorm_id;
        $tenantRoom->room_id = $request->room_id;
        $tenantRoom->status = $request->status;
        $tenantRoom->from_reserve = true;

        $tenantRoom->save();

        Room::where('id', $request->room_id)->update(['is_available' => false]);

    }

    public function rentRoom(Request $request)
    {
        $tenantRoom = new TenantRoom;

        $tenantRoom->owner_id = $request->owner_id;
        $tenantRoom->tenant_id = $request->tenant_id;
        $tenantRoom->dorm_id = $request->dorm_id;
        $tenantRoom->room_id = $request->room_id;
        $tenantRoom->status = $request->status;

        $tenantRoom->save();

        Room::where('id', $request->room_id)->update(['is_available' => false]);
    }

    public function payRent($id, Request $request)
    {
        $auth = Auth::user();
        $payment = TenantPayments::where('id', $id)->first();
        $application = TenantRoom::where('id', $payment->tenant_room_id)->first();
        $owner = User::where('id', $application->owner_id)->first();
        $room = Room::where('id', $application->room_id)->first();

        $amount = !$payment->partial ? $payment->amount_to_pay : $payment->partial;
        $payment->amount_paid = $amount;

        if($application->status == 'reserve') {
            if($payment->partial) {
                $application->expired_at = Carbon::now()->addDay(7);

            }else {
                $application->expired_at = null;
            }

            $application->save();
        }

        if($payment->partial != null) {
            $payment->partial = null;
            $payment->is_paid = false;
        } else {
            $payment->is_paid = true;
        }

        $payment->save();

        $body = [
			"data" => [
				"attributes" => [
					"amount" => $amount * 100,
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
        session()->put('payment', $payment);
        session()->put('application', $application);
        session()->put('amount', $amount);
        session()->put('method', $request->method);

       return $source;

    }

    public function successPage()
    {
        $source_id = session()->get('source_id');
        $source = session()->get('source');
        $owner = session()->get('owner');
        $payment = session()->get('payment');
        $application = session()->get('application');
        $amount = session()->get('amount');
        $method = session()->get('method');

        $notification = new Notification;

        $notification->user_id = $owner->id;
        $notification->message = "Your received $method of ₱$amount";
        $notification->type = 'Payment';
        $notification->save();

        $client = new \GuzzleHttp\Client(['verify' => false]);

        $response = $client->request('POST', 'https://api.paymongo.com/v1/payments', [
            'body' => '{"data":{"attributes":{"amount":' . $amount * 100 . ',"source":{"id":"' . $source_id . '","type":"source"},"currency":"PHP","description":"'.$method.'"}}}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => "Basic " . base64_encode($owner->sk),
                'content-type' => 'application/json',
            ],
        ]);

        return Inertia::render('Paymongo/Success', [
            'source' => $source,
            'owner' => $owner,
            'payment' => $payment,
            'application' => $application,
            'amount' => $amount,
            'method' => $method
        ]);
    }

    public function failedPage()
    {
        $auth = Auth::user();

        $application = TenantRoom::where('tenant_id', $auth->id)->where('is_active', true)->first();
        $payments = TenantPayments::where('user_id', $auth->id)->where('is_paid', false)->get();

        $owner = null;
        if($application) {
            $owner = User::where('id', $application->owner_id)->first();
        }

        return Inertia::render('Tenant/Payments', [
            'applicaiton' => $application,
            'payments' => $payments,
            'owner' => $owner
        ]);
    }
}
