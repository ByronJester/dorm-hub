<?php

namespace App\Http\Controllers;


use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\
    {
        User, Dorm, Room, Amenity, Rule, Payment, Notification, UserIncomeInformation,
        // TenantApplication, TenantBilling, TenantPayment, TenantReservation, CommonAreas
        Reservation, Application, Billing, UserPayment, Tenant, CommonAreas, TenantComplaint, Refund, ContactUs, Service,
        SubscriptionPayment
};
use App\Http\Requests\{ SaveDorm };
use App\Rules\{RoomRule, CommonAreasRule};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\XenditService;


class OwnerController extends Controller
{
    public function dormList()
    {
        $auth = Auth::user();

        if($auth->first_logged_in) {
            return redirect()->route('owner.addDorm');
        }

        $dorms = Dorm::where('user_id', Auth::user()->id)->get();

        return Inertia::render('Owner/Dorms', [
            'dorms' => $dorms,
        ]);
    }

    public function applications()
    {
        $auth = Auth::user();

        if($auth->first_logged_in) {
            return redirect()->route('owner.addDorm');
        }

        $applicationStatuses = ['pending', 'declined'];
        $applications = Application::with(['dorm', 'room', 'owner', 'tenant'])
            ->where('owner_id', $auth->id)->where('is_active', true)->whereIn('status', $applicationStatuses)->get();

        return Inertia::render('Owner/ApplicationModule', [
            'applications' => $applications,


        ]);
    }

    public function reservation()
    {
        $auth = Auth::user();

        if($auth->first_logged_in) {
            return redirect()->route('owner.addDorm');
        }
        $reservations = Reservation::with(['dorm', 'room', 'owner_user', 'tenant_user'])
            ->where('owner', $auth->id)->get();

        $contact = ContactUs::first();
        return Inertia::render('Owner/Reservation', [
            'contact' => $contact,
            'reservations' => $reservations

        ]);
    }

    public function tenants()
    {
        $auth = Auth::user();

        if($auth->first_logged_in) {
            return redirect()->route('owner.addDorm');
        }

        $dorms = Dorm::with(['rooms' => function ($query) {
                $query->where('is_available', true);
            }])
            ->where('user_id', $auth->id)
            ->get(['id', 'property_name', 'status']);

        $applications = Tenant::with('room')->where('is_active', true)
            ->where('owner', $auth->id)->get();

        $monthlyFeeBalances = [];

        foreach ($applications as $application) {
            $balance = Billing::where('profile_id', $application->profile_id)->where('is_paid', false)->where('description', 'Monthly Fee')->sum('amount');
            $billings = Billing::where('f_id', $application->id)->get();

            array_push($monthlyFeeBalances, [
                "balance" => $balance,
            ]);

        }

// $monthlyFeeBalances now contains the fee balances for each tenant

    return Inertia::render('Owner/Tenants', [
        'tenants' => $applications,
        'dorms' => $dorms,
        'feeBalances' => $monthlyFeeBalances
    ]);

    }

    public function maintenance()
    {
        $auth = Auth::user();

        if($auth->first_logged_in) {
            return redirect()->route('owner.addDorm');
        }

        $dorms = DB::table('dorms')->where('user_id', $auth->id)->get(['id', 'property_name']);

        $complaints = TenantComplaint::with(['tenant'])->whereHas('tenant', function($query) use ($auth) {
            $query->where('owner', $auth->id);
        })->get();

        $refundArr = [];

        $refunds = Refund::get();

        foreach ($refunds as $refund) {
            $payment = UserPayment::where('id', $refund->user_payment_id)->first();

            if(!!$payment->tenant_id) {
                $tenant = Tenant::where('id', $payment->tenant_id)->first();

                if($tenant->owner == $auth->id) {
                    array_push($refundArr, [
                        'dorm_id' => $tenant->dorm_id,
                        'payment_id' => $refund->user_payment_id,
                        'refund_id' => $refund->id,
                        'refund_description' => $payment->description,
                        'payment_method' => $refund->payment_method,
                        'wallet_name' => $refund->wallet_name,
                        'account_number' => $refund->account_number,
                        'account_name' => $refund->account_name,
                        'amount' => $refund->amount,
                        'status' => $refund->status,
                        'refund_date' => Carbon::parse($refund->created_at)->isoFormat('LL'),
                    ]);
                }
            }

        }

        $moveouts = Tenant::where('owner', $auth->id)
            ->where('status', 'pending_move_out')
            ->get(['id', 'reason', 'reason_description', 'status', 'move_out']);

        return Inertia::render('Owner/Maintenance', [
            'complaints' => $complaints,
            'dorms' => $dorms,
            'refunds' => $refundArr,
            'moveouts' => $moveouts

        ]);
    }
    public function tenantHistory($tenant_id)
    {
        $tenant = Tenant::with(['room'])->where('profile_id', $tenant_id)->first();

        if (!$tenant) {
            // Handle the case where the tenant is not found
            return response()->json(['error' => 'Tenant not found.'], 404);
        }

        $billings = Billing::where('profile_id', $tenant->profile_id)->get();

        if ($billings->isEmpty()) {
            // Handle the case where there are no billings for the given profile_id
            return response()->json(['message' => 'No billings found.']);
        }

        $payments = [];

        foreach ($billings as $billing) {
            $payment = UserPayment::where('invoice_number', $billing->invoice_number)->first();
            $room = (object) $tenant->room;

            if ($payment) {
                array_push($payments, [
                    'billing_id' => $billing->id,
                    'payment_id' => $payment->id,
                    'room' => $room->name,
                    'description' => $billing->description,
                    'invoice_no' => $billing->invoice_number,
                    'payment_method' => $payment->payment_method,
                    'payment_date' => $billing->date,
                    'amount' => $billing->amount,
                    'status' => $billing->is_paid ? 'Paid' : 'Unpaid',
                    'action' => 'action'
                ]);
            }
        }



        $ebs = Billing::where('subject', 'Electricity')->where('is_paid', false)
            ->where('profile_id', $tenant->profile_id)
            ->get();

        $electricityBillings = null;
        $ebAmount = 0;
        foreach($ebs as $eb) {
            $electricityBillings = $eb;
            $ebAmount += $eb->amount;
        }

        if($electricityBillings) {
            $electricityBillings->amount = $ebAmount;
        }

        $wbs = Billing::where('subject', 'Water')->where('is_paid', false)
            ->where('profile_id', $tenant->profile_id)
            ->get();

        $waterBillings = null;
        $wbAmount = 0;
        foreach($wbs as $wb) {
            $waterBillings = $wb;
            $wbAmount += $wb->amount;
        }

        if($waterBillings) {
            $waterBillings->amount = $wbAmount;
        }

        $ibs = Billing::where('subject', 'Internet')->where('is_paid', false)
            ->where('profile_id', $tenant->profile_id)
            ->get();

        $internetBillings = null;
        $ibAmount = 0;
        foreach($ibs as $ib) {
            $internetBillings = $ib;
            $ibAmount += $ib->amount;
        }

        if($internetBillings) {
            $internetBillings->amount = $ibAmount;
        }

        $mbs = Billing::where('description', 'Monthly Fee')->where('is_paid', false)
            ->where('profile_id', $tenant->profile_id)
            ->get();

        $monthlyFee = null;
        $mbAmount = 0;
        foreach($mbs as $mb) {
            $monthlyFee = $mb;
            $mbAmount += $mb->amount;
        }

        if($monthlyFee) {
            $monthlyFee->amount = $mbAmount;
        }

        $obs = Billing::where('subject', 'Others')->where('is_paid', false)
            ->where('profile_id', $tenant->profile_id)
            ->get();

        $othersBillings = null;
        $obAmount = 0;
        foreach($obs as $ob) {
            $othersBillings = $ib;
            $obAmount += $ob->amount;
        }

        if($othersBillings) {
            $othersBillings->amount = $obAmount;
        }

        return Inertia::render('Owner/TenantsPaymentHistory', [
            'payments' => $payments,
            'tenant' => $tenant,
            'electricity'=> $electricityBillings,
            'water'=> $waterBillings,
            'other'=> $othersBillings,
            'internet'=> $internetBillings,
            'monthly'=> $monthlyFee,
        ]);
    }

    public function addDorm()
    {

        return Inertia::render('Owner/AddDorm', [

        ]);
    }



    public function saveDorm(Request $request)
    {
        $auth = Auth::user();

        $req = [
            'map_address' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'detailed_address' => 'required',
            'property_name' => 'required',
            'description' => 'required',
            'floors_total' => 'required',
            'rooms_total' => 'required',
            'dorm_image' => 'required',
            'business_permit_image_src' => 'required',
            'short_term' => 'required',
            'mix_gender' => 'required',
            'curfew' => 'required',
            'terms' => 'required',
        ];

        if($request->curfew == 'Yes') {
            $req['curfew_hours'] = 'required';
        }

        if($request->short_term == 'Yes') {
            $req['minimum_stay'] = 'required';
        }

        if($auth->first_logged_in) {
            $req['sk'] = 'required';
            $req['pk'] = 'required';

        }

        $validator = Validator::make($request->all(), $req);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $sk = 'xnd_development_2hh1kPCMyT6d7sHYBRItuUTcP3v1ukfXAHz6WKBjosbZkR0RtLtxeZTw2TPaX5Zr';

        $amount = 0;
        $description = 'Dorm Subscription: ' . $request->subscription;
        $action = 'addDorm';

        switch($request->subscription) {
            case 'starter':
                $amount = 300;
                break;
            case 'basic':
                $amount = 500;
                break;
            case 'plus':
                $amount = 1000;
                break;
        }

        $xenditService = new XenditService($sk);
        $response = $xenditService->create($amount, $description, $action);

        if($auth->first_logged_in) {
            auth()->user()->update([
                'sk' => $request->sk,
                'pk' => $request->pk
            ]);
        }

        $dorm = null;

        if($id = $request->id) {
            $dorm = Dorm::where('id', $id)->first();

            if($dorm) {
                Amenity::where('dorm_id', $id)->delete();
                Rule::where('dorm_id', $id)->delete();
            }
        } else {
            $dorm = new Dorm;
        }

        $dorm->user_id = Auth::user()->id;
        $dorm->map_address = $request->map_address;
        $dorm->detailed_address = $request->detailed_address;
        $dorm->lat = $request->lat;
        $dorm->long = $request->long;
        $dorm->property_name = $request->property_name;
        $dorm->description = $request->description;
        $dorm->floors_total = $request->floors_total;
        $dorm->rooms_total = $request->rooms_total;
        $dorm->terms = $request->terms;
        $dorm->landmark = $request->landmark;
        $dorm->note = $request->note;
        $dorm->reservation = $request->reservation;
        $dorm->status = 'temp';

        if($dorm_image = $request->dorm_image) {

            $filename = Str::random(10) . '_dorm_image';

            $uploadFile = $this->uploadFile($dorm_image, $filename);
            $dorm->dorm_image = $filename;
        }

        if($business_permit_src = $request->business_permit_image_src) {
            $business_permit_src = $request->business_permit_image_src;

            $filename = Str::random(10) . '_business_permit' ;

            $uploadFile = $this->uploadFile($business_permit_src, $filename);
            $dorm->business_permit_image = $filename;
        }

        if($dorm->save()) {
            $rooms = json_decode($request->rooms);

            foreach($rooms as $key => $r) {
                $filename = Str::random(10) . '_room_image';


                if($r->id){
                    $room = Room::where('id', $id)->first();
                } else {
                    $room = new Room;
                }

                $room->dorm_id = $dorm->id;
                $room->name = $r->name;
                $room->type_of_room = $r->type_of_room;
                $room->is_aircon = $r->is_aircon;
                $room->furnished_type = $r->furnished_type;
                $room->fee = $r->fee;
                $room->deposit = $r->deposit;
                $room->advance = $r->advance;
                $room->furnished_desc = $r->furnished_desc;

                $uploadFile = $this->uploadFile($r->src, $filename);
                $room->image = $filename;

                $room->save();
            }

            $commonAreas = json_decode($request->commonAreas);

            foreach($commonAreas as $key => $b) {
                $filename = Str::random(10) . '_areas_image';


                if($b->id){
                    $commonArea = CommonAreas::where('id', $id)->first();
                } else {
                    $commonArea = new CommonAreas;
                }

                $commonArea->dorm_id = $dorm->id;
                $commonArea->name = $b->name;

                $uploadFile = $this->uploadFile($b->src, $filename);
                $commonArea->image = $filename;

                $commonArea->save();
            }

            $amenities = json_decode($request->amenities);

            foreach($amenities as $a) {
                if(!!$a) {
                    $amenity = new Amenity;

                    $amenity->dorm_id = $dorm->id;
                    $amenity->amenity = $a;

                    $amenity->save();
                }
            }

            $services = json_decode($request->services);
            foreach($services as $a) {
                $service = new Service;

                $service->dorm_id = $dorm->id;
                $service->service = $a;


                $service->save();
            }


            $rule = new Rule;

            $rule->dorm_id = $dorm->id;
            $rule->short_term = $request->short_term;
            $rule->mix_gender = $request->mix_gender;
            $rule->curfew = $request->curfew;
            $rule->curfew_hours = $request->curfew_hours;
            $rule->minimum_stay = $request->minimum_stay;

            $rules = [];

            foreach(json_decode($request->rules) as $r) {
                array_push($rules, $r->name);
            }

            $rule->rules = implode(',', $rules);

            $rule->save();


            if(!$auth->subscription) {
                User::where('id', $auth->id)->update([
                    'subscription' => $request->subscription
                ]);
            }


            return $response['invoice_url'];

        }

        return response()->json(['message' => 'Error creating dorm.', 'status' => 500], 422);
    }

    public function updateDorm(Request $request, $id){
        $auth = Auth::user();

        $req = [
            'map_address' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'detailed_address' => 'required',
            'property_name' => 'required',
            'description' => 'required',
            'floors_total' => 'required',
            'rooms_total' => 'required',
            'dorm_image' => 'required',
            'business_permit_image_src' => 'required',
            'short_term' => 'required',
            'mix_gender' => 'required',
            'curfew' => 'required',
            'terms' => 'required',
        ];

        if($request->curfew == 'Yes') {
            $req['curfew_hours'] = 'required';
        }

        if($request->short_term == 'Yes') {
            $req['minimum_stay'] = 'required';
        }

        if($auth->first_logged_in) {
            $req['sk'] = 'required';
            $req['pk'] = 'required';

        }

        $validator = Validator::make($request->all(), $req);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $dorm = Dorm::find($id);

        if (!$dorm) {
            return response()->json(['message' => 'Dorm not found', 'status' => 404], 404);
        }

        $dorm->map_address = $request->map_address;
        $dorm->detailed_address = $request->detailed_address;
        $dorm->lat = $request->lat;
        $dorm->long = $request->long;
        $dorm->property_name = $request->property_name;
        $dorm->description = $request->description;
        $dorm->floors_total = $request->floors_total;
        $dorm->rooms_total = $request->rooms_total;
        $dorm->terms = $request->terms;

        if($dorm_image = $request->dorm_image) {

            $filename = Str::random(10) . '_dorm_image';

            $uploadFile = $this->uploadFile($dorm_image, $filename);
            $dorm->dorm_image = $filename;
        }

        if($business_permit_src = $request->business_permit_image_src) {
            $business_permit_src = $request->business_permit_image_src;

            $filename = Str::random(10) . '_business_permit' ;

            $uploadFile = $this->uploadFile($business_permit_src, $filename);
            $dorm->business_permit_image = $filename;
        }

        if ($dorm->save()) {
            $rooms = json_decode($request->rooms);

            foreach ($rooms as $key => $r) {
                $filename = Str::random(10) . '_room_image';

            if ($r->id) {
                $room = Room::find($r->id);

                if ($room) {
                    // Room with the given ID found, update its attributes
                    $room->name = $r->name;
                    $room->type_of_room = $r->type_of_room;
                    $room->is_aircon = $r->is_aircon;
                    $room->furnished_type = $r->furnished_type;
                    $room->fee = $r->fee;
                    $room->deposit = $r->deposit;
                    $room->advance = $r->advance;
                    $room->furnished_desc = $r->furnished_desc;

                    // Update the room image if a new image is provided
                    if ($r->src) {
                        $uploadFile = $this->uploadFile($r->src, $filename);
                        $room->image = $filename;
                    }

                    $room->save();
                }
            }else{
                $room = new Room;

                $room->dorm_id = $dorm->id;
                $room->name = $r->name;
                $room->type_of_room = $r->type_of_room;
                $room->is_aircon = $r->is_aircon;
                $room->furnished_type = $r->furnished_type;
                $room->fee = $r->fee;
                $room->deposit = $r->deposit;
                $room->advance = $r->advance;
                $room->furnished_desc = $r->furnished_desc;

                // Upload room image if provided
                if ($r->src) {
                    $uploadFile = $this->uploadFile($r->src, $filename);
                    $room->image = $filename;
                }

                $room->save();
             }
            }

            $commonAreas = json_decode($request->commonAreas);
            CommonAreas::where('dorm_id', $dorm->id)->delete();

            foreach ($commonAreas as $key => $b) {
                $filename = Str::random(10) . '_areas_image';

                    $commonArea = new CommonAreas;

                    $commonArea->dorm_id = $dorm->id;
                    $commonArea->name = $b->name;

                    // Upload common area image if provided
                    if ($b->src) {
                        $uploadFile = $this->uploadFile($b->src, $filename);
                        $commonArea->image = $filename;
                    }

                    $commonArea->save();

            }

            // Update or create amenities
            $amenities = json_decode($request->amenities);

            Amenity::where('dorm_id', $dorm->id)->delete();

            foreach ($amenities as $a) {
                $amenity = new Amenity;

                $amenity->dorm_id = $dorm->id;
                $amenity->amenity = $a;

                $amenity->save();
            }

            // Update rules
            $rule = Rule::where('dorm_id', $dorm->id)->first();

            if ($rule) {
                $rule->short_term = $request->short_term;
                $rule->mix_gender = $request->mix_gender;
                $rule->curfew = $request->curfew;
                $rule->curfew_hours = $request->curfew_hours;
                $rule->minimum_stay = $request->minimum_stay;

                // Update rules array
                $rules = [];

                foreach (json_decode($request->rules) as $r) {
                    array_push($rules, $r->name);
                }

                $rule->rules = implode(',', $rules);

                $rule->save();
            }


            return response()->json(['message' => 'Dorm updated successfully.', 'status' => 200], 200);
        }

        return response()->json(['message' => 'Error updating dorm.', 'status' => 500], 500);


    }

    public function dormplus(Request $request)
    {
        $auth = Auth::user();

        $req = [
            'map_address' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'detailed_address' => 'required',
            'property_name' => 'required',
            'description' => 'required',
            'floors_total' => 'required',
            'rooms_total' => 'required',
            'dorm_image' => 'required',
            'business_permit_image_src' => 'required',
            'short_term' => 'required',
            'mix_gender' => 'required',
            'curfew' => 'required',
            'terms' => 'required',
        ];

        if($request->curfew == 'Yes') {
            $req['curfew_hours'] = 'required';
        }

        if($request->short_term == 'Yes') {
            $req['minimum_stay'] = 'required';
        }

        if($auth->first_logged_in) {
            $req['sk'] = 'required';
            $req['pk'] = 'required';

        }

        $validator = Validator::make($request->all(), $req);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }


        if($auth->first_logged_in) {
            auth()->user()->update([
                'sk' => $request->sk,
                'pk' => $request->pk
            ]);
        }

        $dorm = null;

        if($id = $request->id) {
            $dorm = Dorm::where('id', $id)->first();

            if($dorm) {
                Amenity::where('dorm_id', $id)->delete();
                Rule::where('dorm_id', $id)->delete();
            }
        } else {
            $dorm = new Dorm;
        }

        $dorm->user_id = Auth::user()->id;
        $dorm->map_address = $request->map_address;
        $dorm->detailed_address = $request->detailed_address;
        $dorm->lat = $request->lat;
        $dorm->long = $request->long;
        $dorm->property_name = $request->property_name;
        $dorm->description = $request->description;
        $dorm->floors_total = $request->floors_total;
        $dorm->rooms_total = $request->rooms_total;
        $dorm->terms = $request->terms;
        $dorm->landmark = $request->landmark;
        $dorm->note = $request->note;
        $dorm->reservation = $request->reservation;

        if($dorm_image = $request->dorm_image) {

            $filename = Str::random(10) . '_dorm_image';

            $uploadFile = $this->uploadFile($dorm_image, $filename);
            $dorm->dorm_image = $filename;
        }

        if($business_permit_src = $request->business_permit_image_src) {
            $business_permit_src = $request->business_permit_image_src;

            $filename = Str::random(10) . '_business_permit' ;

            $uploadFile = $this->uploadFile($business_permit_src, $filename);
            $dorm->business_permit_image = $filename;
        }

        if($dorm->save()) {
            $rooms = json_decode($request->rooms);

            foreach($rooms as $key => $r) {
                $filename = Str::random(10) . '_room_image';


                if($r->id){
                    $room = Room::where('id', $id)->first();
                } else {
                    $room = new Room;
                }

                $room->dorm_id = $dorm->id;
                $room->name = $r->name;
                $room->type_of_room = $r->type_of_room;
                $room->is_aircon = $r->is_aircon;
                $room->furnished_type = $r->furnished_type;
                $room->fee = $r->fee;
                $room->deposit = $r->deposit;
                $room->advance = $r->advance;
                $room->furnished_desc = $r->furnished_desc;

                $uploadFile = $this->uploadFile($r->src, $filename);
                $room->image = $filename;

                $room->save();
            }

            $commonAreas = json_decode($request->commonAreas);

            foreach($commonAreas as $key => $b) {
                $filename = Str::random(10) . '_areas_image';


                if($b->id){
                    $commonArea = CommonAreas::where('id', $id)->first();
                } else {
                    $commonArea = new CommonAreas;
                }

                $commonArea->dorm_id = $dorm->id;
                $commonArea->name = $b->name;

                $uploadFile = $this->uploadFile($b->src, $filename);
                $commonArea->image = $filename;

                $commonArea->save();
            }

            $amenities = json_decode($request->amenities);

            foreach($amenities as $a) {
                $amenity = new Amenity;

                $amenity->dorm_id = $dorm->id;
                $amenity->amenity = $a;

                $amenity->save();
            }

            $services = json_decode($request->services);
            foreach($services as $a) {
                $service = new Service;

                $service->dorm_id = $dorm->id;
                $service->service = $a;

                $service->save();
            }

            $rule = new Rule;

            $rule->dorm_id = $dorm->id;
            $rule->short_term = $request->short_term;
            $rule->mix_gender = $request->mix_gender;
            $rule->curfew = $request->curfew;
            $rule->curfew_hours = $request->curfew_hours;
            $rule->minimum_stay = $request->minimum_stay;

            $rules = [];

            foreach(json_decode($request->rules) as $r) {
                array_push($rules, $r->name);
            }

            $rule->rules = implode(',', $rules);

            $rule->save();

            return response()->json(['message' => 'Dorm added successfully.', 'status' => 200], 200);
        }

        return response()->json(['message' => 'Error creating dorm.', 'status' => 500], 422);
    }



    public function applicationStatusChange(Request $request)
    {
        $status = $request->status;

        $application = TenantRoom::where('id', $request->id)->first();
        $room = Room::where('id', $application->room_id)->first();

        $notification = new Notification;

        if($status == 'approved') {
            $application->is_approved = true;

            $payment = new TenantPayments;
            $payment->amount_to_pay = $room->fee;

            if($application->status == 'reserve') {
                $payment->partial = 300;
            }

            $payment->tenant_room_id = $application->id;
            $payment->user_id = $application->tenant_id;
            $payment->date = Carbon::now();
            $payment->save();

            $newPayment = new TenantPayments;
            $newPayment->amount_to_pay = $room->advance;

            $newPayment->tenant_room_id = $application->id;
            $newPayment->user_id = $application->tenant_id;
            $newPayment->date = Carbon::now()->addMonth();
            $newPayment->save();

            $notification->user_id = $application->tenant_id;
            $notification->message = 'Your rental application has been approved.';
            $notification->type = 'Rental Application';
            $notification->redirection = 'tenant.payments';
        }

        if($status == 'declined') {
            $application->is_active = false;
            $room->is_available = true;
            $room->save();

            $notification->user_id = $application->tenant_id;
            $notification->message = 'Your rental application has been declined.';
            $notification->type = 'Rental Application';
        }

        $application->save();
        $notification->save();

        return response()->json(['message' => $status], 200);
    }

    public function paymentMarkAsPaid(Request $request)
    {
        $id = $request->id;

        $payment = TenantPayments::where('id', $id)->first();
        $application = TenantRoom::where('id', $payment->tenant_room_id)->first();

        $payment->mode_of_payment = 'Cash';

        if($payment->pending_payment) {
            $payment->mode_of_payment = 'Bank';

            if($payment->amount_paid != null) {
                $payment->amount_paid = $payment->amount_paid + $payment->pending_payment;
            } else {
                $payment->amount_paid = $payment->pending_payment;
            }

            if($payment->partial) {
                $payment->pending_payment = $payment->amount_to_pay - 300;
            }

        } else {
            if($payment->partial) {
                $payment->amount_paid = $payment->partial;
            } else {
                $payment->amount_paid = $payment->amount_to_pay;
            }
        }


        $payment->partial = null;
        $payment->pending_payment = null;
        $payment->is_paid = $payment->amount_to_pay == $payment->amount_paid;
        $payment->save();

        $application->expired_at = null;
        $application->save();

        $notification = new Notification;
        $notification->user_id = $application->tenant_id;
        $message = "Your amount to pay of ₱%s has been mark as paid by dorm owner.";
        $notification->message = sprintf($message, $payment->amount_paid);
        $notification->type = 'Rental Payment';
        $notification->save();

        return response()->json(['message' => $payment], 200);
    }

    public function dashboard()
    {
        $user = Auth::user();

        if($user->first_logged_in) {
            return redirect()->route('owner.addDorm');
        }

        $applications = Tenant::where('owner', $user->id);

        $currentMonth = Carbon::now()->month;
        $paidAmount = 0;
        $unpaidAmount = 0;

        foreach($applications->where('is_active', true)->where('status', 'approved')->get() as $application) {
            $application = (object) $application;
            $payments = UserPayment::where('profile_id', $application->profile_id)->get();

            foreach($payments as $payment) {
                $paymentMonth = Carbon::parse($payment->for_the_month)->month;

                if($payment->is_paid) {
                    $paidAmount += $payment->amount;
                } else {
                    $paidAmount += $payment->amount;
                }
            }
        }

        $applications = Application::where('owner_id', $user->id)->where('is_active', true)->count();
        $tenants = Tenant::where('owner', $user->id)->where('is_active', true)->count();

        return Inertia::render('Owner/Dashboard', [
            'paidAmount' => $paidAmount,
            'unpaidAmount' => $unpaidAmount,
            'totalTenants' => $tenants,
            'totalApplications' => $applications
        ]);
    }

    public function subscription(){
        $auth = Auth::user();
        $subcriptionPayments = SubscriptionPayment::where('owner_id', $auth->id)->get();

        return Inertia::render('Owner/Subscription', [
            'subscriptionPayments' => $subcriptionPayments
        ]);
    }

    public function reports()
    {
        $auth = Auth::user();

        if($auth->first_logged_in) {
            return redirect()->route('owner.addDorm');
        }

        $now = Carbon::now();

        $dorms = DB::table('dorms')->where('user_id', $auth->id)->get();

        $contact = ContactUs::first();

        $dormReports = [];

        foreach($dorms as $dorm) {

            $ts = Tenant::where('dorm_id', $dorm->id)->get();

            $monthlyIncome = 0;
            $yearlyIncome = 0;

            foreach($ts as $t) {
                $monthlyIncome += Billing::where('profile_id', $t->profile_id)->whereMonth('for_the_month', $now)->sum('amount');
                $yearlyIncome += Billing::where('profile_id', $t->profile_id)->whereYear('for_the_month', $now)->sum('amount');
            }

            array_push($dormReports, [
                'name' => $dorm->property_name,
                'date_registered' => Carbon::parse($dorm->created_at)->isoFormat('LLL'),
                'rooms_total' => $dorm->rooms_total,
                'occupied_rooms' => Room::where('dorm_id', $dorm->id)->where('is_available', false)->count(),
                'vacant_rooms' => Room::where('dorm_id', $dorm->id)->where('is_available', true)->count(),
                'monthly_income' => $monthlyIncome,
                'yearly_income' => $yearlyIncome,
                'created_at' => $dorm->created_at
            ]);
        }

        $reservationReports = [];

        $reservations = Reservation::with(['tenant_user', 'room'])
            ->where('owner', $auth->id)
            ->where('is_active', true)->get();

        foreach($reservations as $reservation) {
            $tenant = (object) $reservation->tenant_user;
            $room = (object) $reservation->room;

            array_push($reservationReports, [
                'name' => $tenant->name,
                'room' => $room->name,
                'fee' => 300,
                'expiration_date' => Carbon::parse($reservation->expired_at)->isoFormat('LL'),
                'created_at' => $reservation->created_at
            ]);
        }

        $tenantReports = [];
        $requestReports = [];
        $occupancyReports = [];
        $rentRollReports = [];
        $incomeReports = [];

        $allTenants = Tenant::with(['room', 'tenant_user'])
            ->where('owner', $auth->id)->get();

        foreach($allTenants as $at) {
            $tenant = (object) $at->tenant_user;
            $room = (object) $at->room;


            array_push($tenantReports, [
                'name' => $at->profile->name,
                'contact' => $tenant->phone_number,
                'room' => $room->name,
                'fee' => $room->fee,
                'move_in' => Carbon::parse($at->move_in)->isoFormat('LL'),
                'move_out' => Carbon::parse($at->move_out)->isoFormat('LL'),
                'created_at' => $at->created_at
            ]);

            if(!!$at->is_active) {
                array_push($occupancyReports, [
                    'room' => $room->name,
                    'name' => $tenant->name,
                    'move_in' => Carbon::parse($at->move_in)->isoFormat('LL'),
                    'status' => $at->status,
                    'created_at' => $at->created_at
                ]);
            }

            $billings = Billing::where('profile_id', $at->profile_id)->get();
            $totalRentCollected = 0;
            $otherCharges = 0;

            foreach($billings as $billing) {
                if(!!$billing->is_paid) {
                    if($billing->description == 'monthly_fee' || $billing->description == 'advance_and_deposit_fee') {
                        $totalRentCollected += $billing->amount;
                    }

                    if($billing->description != 'monthly_fee' && $billing->description != 'advance_and_deposit_fee') {
                        $otherCharges += $billing->amount;
                    }
                }
            }

            array_push($rentRollReports, [
                'room' => $room->name,
                'name' => $tenant->name,
                'fee' => $room->fee,
                'totalRentCollected' => $totalRentCollected,
                'created_at' => $at->created_at
            ]);

            array_push($incomeReports, [
                'room' => $room->name,
                'name' => $at->profile->name,
                'move_in' => Carbon::parse($at->move_in)->isoFormat('LL'),
                'total_rent_collected' => $totalRentCollected,
                'other_charges' => $otherCharges,
                'total_income' => $totalRentCollected + $otherCharges,
                'created_at' => $at->created_at
            ]);

            $complaints = TenantComplaint::where('tenant_id', $at->id)->get();

            foreach($complaints as $complaint) {
                array_push($requestReports, [
                    'subject' => $complaint->subject,
                    'complain' => $complaint->complain,
                    'status' => $complaint->status,
                    'request_date' => Carbon::parse($complaint->created_at)->isoFormat('LL'),
                    'date_finish' => $complaint->status == 'finish' ? Carbon::parse($complaint->updated_at)->isoFormat('LL') : null,
                    'created_at' => $complaint->created_at
                ]);
            }
        }

        return Inertia::render('Owner/Report', [
            'contact' => $contact,
            'dorms' => $dorms,
            'dormReports' => $dormReports,
            'reservationReports' => $reservationReports,
            'tenantReports' => $tenantReports,
            'requestReports' => $requestReports,
            'occupancyReports' => $occupancyReports,
            'rentRollReports' => $rentRollReports,
            'incomeReports' => $incomeReports
        ]);
    }

    public function billings()
    {
        $auth = Auth::user();

        if($auth->first_logged_in) {
            return redirect()->route('owner.addDorm');
        }

        $billTenants = [];
        $billingHistory = [];

        $reservations = Reservation::with(['dorm', 'room', 'tenant_user'])
            ->where('owner', $auth->id)->get();

        foreach($reservations as $reservation) {
            $room = (object) $reservation->room;
            $tenant = (object) $reservation->tenant_user;
            $billings = Billing::where('f_id', $reservation->id)->get();


            foreach ($billings as $billing) {
                $payment = UserPayment::where('invoice_number', $billing->invoice_number)->first();

                array_push($billingHistory, [
                    "tenant_id" => $reservation->id,
                    "room_id" => $room->id,
                    "room" => $room->name,
                    "tenant" => $tenant->name,
                    "description" => $billing->description,
                    "amount" => $billing->amount,
                    "invoice_no" => $billing->invoice_number,
                    "payment_method" => $payment ? $payment->payment_method : null,
                    "status" => $billing->is_paid ? 'Paid' : 'Unpaid',
                    "dorm_id" => $reservation->dorm_id,
                    'created_at' => $billing->created_at,
                    "auto_bill" => false
                ]);
            }

        }

        $applications = Tenant::with(['dorm', 'room'])
            ->where('owner', $auth->id)
            ->where('is_active', true)
            ->get();


        foreach ($applications as $application) {
            $tenant = (object) $application->profile;
            $room = (object) $application->room;
            $balance = Billing::where('profile_id', $application->profile_id)->where('is_paid', false)->sum('amount');
            $billings = Billing::where('f_id', $application->id)->get();

            array_push($billTenants, [
                "tenant_id" => $application->id,
                "room_id" => $room->id,
                "room" => $room->name,
                "tenant" => $tenant->name,
                "monthly_fee" => $room->fee,
                "balance" => $balance,
                "move_in" => $application->move_in,
                "dorm_id" => $application->dorm_id,
                "action" => "action",
                "auto_bill" => $application->auto_bill == 1 ? true : false,
                'profile_id' => $application->profile_id
            ]);

            foreach ($billings as $billing) {
                $payment = UserPayment::where('invoice_number', $billing->invoice_number)->first();

                array_push($billingHistory, [
                    "tenant_id" => $application->id,
                    "room_id" => $room->id,
                    "room" => $room->name,
                    "tenant" => $tenant->name,
                    "description" => $billing->description,
                    "amount" => $billing->amount,
                    "invoice_no" => $billing->invoice_number,
                    "payment_method" => $payment ? $payment->payment_method : null,
                    "status" => $billing->is_paid ? 'Paid' : 'Unpaid',
                    "dorm_id" => $application->dorm_id,
                    "auto_bill" => $application->auto_bill == 1 ? true : false
                ]);
            }
        }

        return Inertia::render('Owner/Billings', [
            'billTenants' => $billTenants,
            'billingHistory' => $billingHistory,
            'dorms' => DB::table('dorms')->where('user_id', $auth->id)
                ->where('status', 'approved')->get()
        ]);
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

    public function declineApplication($id, Request $request)
    {
        $application = Application::with(['tenant'])->where('id', $id)->first();

        $reason = $request->input('reason');

        $application->status = 'declined';
        $application->reason = $reason;
        $application->save();

        $tenant = (object) $application->tenant;
        $this->sendSMS($tenant->phone_number, "Your application has been declined.");

        return Room::where('id', $request->room_id)->update([
            'status' => null,
            'is_available' => true
        ]);
    }

    public function declineReservation($id, Request $request)
    {
        TenantApplication::where('id', $id)->update([
            'status' => 'declined',
            'is_active' => false
        ]);

        TenantReservation::where('id', $request->reservation_id)->update([
            'is_approved' => false
        ]);

        return true;
    }

    public function approveApplication($id, Request $request)
    {
        $now = Carbon::now();

        $auth = Auth::user();

        $room = Room::where('id', $request->room_id)->first();

        $room->status='rent';
        $room->save();

        $application = Application::with(['tenant'])->where('id', $id)->first();

        $reservation = Reservation::where('room_id', $room->room_id)->first();
        if($reservation){
            $reservation->is_active = false;
            $reservation->save();
        }

        $tenant = (object) $application->tenant;
        $this->sendSMS($tenant->phone_number, "Your application has been approved.");

        $billing = Billing::create([
            'f_id' => $tenant->id,
            'profile_id' => $application->profile_id,
            'user_id' => $application->tenant_id,
            'amount' => (int) $room->deposit + (int) $room->advance,
            'description' => 'Advance and Deposit Fee',
            'type' => 'application',
            'is_paid' => false,
            'payment_date' => null,
            'for_the_month' => Carbon::now(),
            'is_active' => true
        ]);

        return true;
    }

    public function approveReservation($id, Request $request)
    {
        $now = Carbon::now();

        $room = Room::where('id', $request->room_id)->first();

        TenantApplication::where('id', $id)->update([
            'status' => 'rent',
        ]);

        TenantReservation::where('id', $request->reservation_id)->update([
            'is_approved' => true
        ]);

        $reservationBill = TenantBilling::where('tenant_application_id', $request->tenant_application_id)
            ->where('description', 'reservation_fee')
            ->first();

        $reservationBill->is_paid = true;

        $reservationBill->save();

        TenantPayment::where('tenant_billing_id', $reservationBill->id)->update([
            'status' => 'paid'
        ]);

        // $billing = TenantBilling::create([
        //     'tenant_id' => $request->tenant_id,
        //     'tenant_application_id' => $request->tenant_application_id,
        //     'first_name' => $request->first_name,
        //     'last_name' => $request->last_name,
        //     'phone_number' => $request->phone_number,
        //     'amount' => (int) $room->deposit + (int) $room->advance,
        //     'description' => 'advance_and_deposit_fee',
        //     'date' => $now,
        // ]);

        // TenantPayment::create([
        //     'tenant_id' => $request->tenant_id,
        //     'tenant_billing_id' => $billing->id,
        //     'dorm_id' => $room->dorm_id,
        //     'room_id' => $room->id,
        //     'amount' => (int) $room->deposit + (int) $room->advance,
        //     'category' => 'advance_and_deposit_fee',
        //     'date' => $now
        // ]);

        return true;
    }

    public function paymentHistoryMarkAsPaid(Request $request)
    {
        Billing::where('id', $request->billing_id)->update([
            'is_paid' => true
        ]);

        UserPayment::where('id', $request->payment_id)->update([
            'status' => 'paid'
        ]);

        return true;
    }

    public function submitManualBill(Request $request)
    {
        $req = [
            'tenant_id' => 'required',
            'profile_id' => 'required',
            'subject' => 'required',
            'description' => 'required',
            'amount' => 'required',
        ];

        $validator = Validator::make($request->all(), $req);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $tenant = Tenant::where('id', $request->tenant_id)->first();

        $user = User::where('id', $tenant->tenant)->first();

        $billing = Billing::create([
            'f_id' => $tenant->id,
            'profile_id' => $request->profile_id,
            'user_id' => $tenant->tenant,
            'amount' => $request->amount,
            'description' => $request->description,
            'subject' => $request->subject,
            'type' => 'rent',
            'is_paid' => false,
            'payment_date' => null,
            'for_the_month' => null,
            'is_active' => true
        ]);

        return true;
    }

    public function submitAutoBill(Request $request)
    {
        $tenant = Tenant::where('id', $request->tenant_id)->first();
        $room = Room::where('id', $tenant->room_id)->first();

        $tenant->auto_bill = $request->auto_bill;

        if(!!$request->auto_bill) {
            if($tenant->auto_bill_date == null) {
                $tenant->auto_bill_date = Carbon::parse($tenant->move_in)->addMonthsNoOverflow(1);

                $billing = Billing::create([
                    'f_id' => $tenant->id,
                    'profile_id' => $tenant->profile_id,
                    'user_id' => $tenant->tenant,
                    'amount' => $room->fee,
                    'description' => 'Monthly Fee',
                    'type' => 'rent',
                    'is_paid' => false,
                    'payment_date' => null,
                    'for_the_month' => Carbon::parse($tenant->move_in)->addMonthsNoOverflow(1),
                    'is_active' => false
                ]);
            }
        }

        $tenant->save();

        return $tenant;
    }

    public function changeRoomStatus(Request $request)
    {
        return Room::where('id', $request->id)->update([
            'is_available' => !$request->is_available
        ]);
    }

    public function changeComplainStatus($id, Request $request)
    {
        return TenantComplaint::where('id', $id)->update([
            'status' => $request->status
        ]);
    }

    public function changeTenantStatus($id, Request $request)
    {
        return Tenant::where('profile_id', $id)->update([
            'is_delinquent' => false
        ]);
    }

    public function changeTenantStatusActive($id, Request $request)
    {
        return Tenant::where('profile_id', $id)->update([
            'is_delinquent' => true
        ]);
    }


    public function refundChangeStatus($status, Request $request)
    {
        $refund = Refund::where('id', $request->id)->first();

        if($request->has('proof_of_refund')) {
            $proof_of_refund = $request->proof_of_refund;

            $filename = Str::random(10) . '_proof_of_refund';

            $uploadFile = $this->uploadFile($proof_of_refund, $filename);
            $refund->proof_of_refund = $filename;
        }

        if($status == 'declined') {
            UserPayment::where('id', $refund->user_payment_id)->update([
                'status' => 'declined_refund'
            ]);
        }

        if($status == 'ongoing') {
            UserPayment::where('id', $refund->user_payment_id)->update([
                'status' => 'ongoing_refund'
            ]);
        }

        if($status == 'refunded') {
            UserPayment::where('id', $refund->user_payment_id)->update([
                'status' => 'refunded'
            ]);
        }

        $refund->status = $status;

        return $refund->save();
    }

    public function approveMoveOut(Request $request)
    {
        $tenant = Tenant::where('id', $request->id)->first();

        $application = Application::where('owner_id', $tenant->owner)
            ->where('tenant_id', $tenant->tenant)
            ->where('dorm_id', $tenant->dorm_id)
            ->where('room_id', $tenant->room_id)
            ->first();

        $application->is_active = false;
        $application->save();

        $tenant->status = 'moved_out';
        $tenant->is_active = false;

        return $tenant->save();
    }

    public function noticeTermination(Request $request)
    {
        $tenant = Tenant::where('id', $request->id)->first();

        $user = User::where('id', $tenant->tenant)->first();

        $message = 'You are been notify for termination.';
        $this->sendSMS($user->phone_number, $message);

        return true;
    }

    public function removeTenant(Request $request)
    {
        $tenant = Tenant::where('id', $request->id)->first();

        $application = Application::where('owner_id', $tenant->owner)
            ->where('tenant_id', $tenant->tenant)
            ->where('dorm_id', $tenant->dorm_id)
            ->where('room_id', $tenant->room_id)
            ->first();

        $application->is_active = false;
        $application->save();

        $tenant->status = 'moved_out';
        $tenant->is_active = false;

        return $tenant->save();
    }

    public function addTenant(Request $request)
    {
        $req = [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'phone_number' => 'required',
            'id_picture' => 'required',
            'selfie_id_picture' => 'required',
            'source_of_income' => 'required',
            'monthly_income' => 'required',
            'proof' => 'required',
            'image' => 'required',
            'room' => 'required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ];

        $validator = Validator::make($request->all(), $req);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->phone_number = $request->phone_number;
        $user->password = Hash::make($request->phone_number);
        $user->is_approved = false;
        $user->first_logged_in = false;

        $filenameProfileImage = Str::random(10) . '_profile_image';
        $uploadFile = $this->uploadFile($request->image, $filenameProfileImage);
        $user->image = $filenameProfileImage;

        $filenameIdPicture = Str::random(10) . '_id_picture';
        $uploadFile = $this->uploadFile($request->id_picture, $filenameIdPicture);
        $user->id_picture = $filenameIdPicture;

        $filenameSelfieIdPicture = Str::random(10) . '_selfie_picture';
        $uploadFile = $this->uploadFile($request->selfie_id_picture, $filenameSelfieIdPicture);
        $user->selfie_id_picture = $filenameSelfieIdPicture;

        $user->save();

        $incomeInfo = new UserIncomeInformation;
        $incomeInfo->user_id = $user->id;
        $incomeInfo->source_of_income = $request->source_of_income;
        $incomeInfo->monthly_income = $request->monthly_income;

        $filenameProof = Str::random(10) . '_selfie_picture';
        $uploadFile = $this->uploadFile($request->proof, $filenameProof);
        $incomeInfo->proof = $filenameProof;

        $incomeInfo->save();

        $room = Room::where('id', $request->room)->first();
        $dorm = Dorm::where('id', $room->id)->first();

        $room->is_available = false;
        $room->status = 'rent';
        $room->save();


        $application = new Application;
        $application->owner_id = $dorm->user_id;
        $application->tenant_id = $user->id;
        $application->dorm_id = $dorm->id;
        $application->room_id = $room->id;
        $application->status = 'pending';
        $application->move_in = Carbon::now();

        $application->save();

        return true;
    }

    public function getOwnerStatus()
    {
        $user = Auth::user();

        return response()->json($user->status, 200);
    }

    public function addDormSuccessPage($invoice)
    {

        $dorm = Dorm::where('status', 'temp')->first();

        if($dorm){
            $dorm->status = 'pending';
            $dorm->save();
        }

        $auth = Auth::user();

        if($auth->first_logged_in) {
            auth()->user()->update([
                'first_logged_in' => false
            ]);
        }

        $sk = 'xnd_development_2hh1kPCMyT6d7sHYBRItuUTcP3v1ukfXAHz6WKBjosbZkR0RtLtxeZTw2TPaX5Zr';
        $xenditService = new XenditService($sk);

        $response = $xenditService->get($invoice);

        $dorms = Dorm::where('status', 'pending')
            ->where('user_id', auth()->user()->id)
            ->latest()
            ->first();

        $subscription = $auth->subscription;
        $amount = 0;

        if($subscription) {
            switch ($subscription) {
                case 'starter':
                    $amount = 300;
                    break;
                case 'basic':
                    $amount = 500;
                    break;
                case 'plus':
                    $amount = 1000;
                    break;
            }
        }


        if($dorm) {
            SubscriptionPayment::updateOrCreate(
                ['invoice_number' => $invoice],
                [
                    'subscription' => $subscription,
                    'amount' => $amount,
                    'owner_id' => $dorm->user_id,
                    'invoice_number' => $invoice,
                    'is_paid' => true,
                    'for_the_month' => Carbon::now()
                ]
            );
        }



        return Inertia::render('Xendit/Success', [
            'dorm' => $dorms,
            'invoice' => $response['data'][0]
        ]);

    }


    public function triggerAutoBill(Request $request)
    {
        $tenant = (object) $request;
        $room = (object) $tenant->room;

        $latestBilling = Billing::where('profile_id', $tenant->profile_id)
            ->where('description', 'Monthly Fee')
            ->latest()
            ->first();


        if($latestBilling) {
            $latestDate = Carbon::parse($latestBilling->for_the_month);

            $billing = Billing::create([
                'user_id' => $tenant->tenant,
                'amount' => $room->fee,
                'description' => 'Monthly Fee',
                'for_the_month' => $latestDate->addMonthsNoOverflow(1),
                'f_id' => $tenant->id,
                'profile_id' => $tenant->profile_id,
                'type' => 'rent',
                'is_active' => true,
                'is_paid' => false,
                'payment_date' => null,
            ]);
        } else {
            $latestDate = Carbon::parse($tenant->auto_bill_date);

            $billing = Billing::create([
                'user_id' => $tenant->tenant,
                'amount' => $room->fee,
                'description' => 'Monthly Fee',
                'for_the_month' => $latestDate->addMonthsNoOverflow(1),
                'f_id' => $tenant->id,
                'profile_id' => $tenant->profile_id,
                'type' => 'rent',
                'is_active' => true,
                'is_paid' => false,
                'payment_date' => null,
            ]);
        }


        return response()->json('Success', 200);

    }

    public function markAsDue(Request $request)
    {
        $profile_id = $request->profile_id;
        $type = $request->type;

        $billings = Billing::where('profile_id', $profile_id);

        if($type == 'Monthly Fee') {
            $billings = $billings->where('description', $type);
        } else {
            $billings = $billings->where('subject', $type);
        }

        return $billings->update([
            'is_overdue' => true
        ]);
    }
}
