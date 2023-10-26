<?php

namespace App\Http\Controllers;


use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\{ Dorm, Room, Amenity, Rule, Payment, TenantRoom, TenantPayments, Notification };
use App\Http\Requests\{ SaveDorm };
use App\Rules\RoomRule;
use Carbon\Carbon;

class OwnerController extends Controller
{
    public function dormList()
    {
        $dorms = Dorm::where('user_id', Auth::user()->id)->get();

        return Inertia::render('Owner/Dorms', [
            'dorms' => $dorms,
        ]);
    }
    
    public function applications()
    {
        $user = Auth::user();

        $applications = TenantRoom::with('payments')->where('owner_id', $user->id)->where('is_active', true)->get();

        return Inertia::render('Owner/ApplicationModule', [
            'applications' => $applications,
        ]);
    }

    public function tenants()
    {

        return Inertia::render('Owner/Tenants', [
            
        ]);
    }

    public function maintenance()
    {

        return Inertia::render('Owner/Maintenance', [
            
        ]);
    }



    public function saveDorm(Request $request)
    {
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
            'business_permit_image' => 'required',
            // 'rooms' => ['required', new RoomRule],
            // 'amenities' => 'required|array|between:1,8',
            'short_term' => 'required',
            'mix_gender' => 'required',
            'curfew' => 'required',
            // 'rules' => 'required|array|between:1,20',
            // 'range_from' => 'required',
            // 'range_to' => 'required',
            // 'payments' => 'required|array'
        ];

        if($request->curfew == 'Yes') {
            $req['curfew_hours'] = 'required';
        }

        if($request->short_term == 'Yes') {
            $req['minimum_stay'] = 'required';
        }

        $validator = Validator::make($request->all(), $req);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $dorm = null;

        if($id = $request->id) {
            $dorm = Dorm::where('id', $id)->first();

            if($dorm) {
                Amenity::where('dorm_id', $id)->delete();
                Rule::where('dorm_id', $id)->delete();
                // Payment::where('dorm_id', $id)->delete();
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

        if($dorm_image = $request->dorm_image) {

            $filename = Str::random(10) . '_dorm_image';

            $uploadFile = $this->uploadFile($dorm_image, $filename);
            $dorm->dorm_image = $filename;
        }

        if($business_permit_image = $request->business_permit_image) {
            $business_permit_image = $request->business_permit_image;

            $filename = Str::random(10) . '_business_permit' ;

            $uploadFile = $this->uploadFile($business_permit_image, $filename);
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

                $uploadFile = $this->uploadFile($r->src, $filename);
                $room->image = $filename;

                $room->save();
            }

            $amenities = json_decode($request->amenities);

            foreach($amenities as $a) {
                $amenity = new Amenity;

                $amenity->dorm_id = $dorm->id;
                $amenity->amenity = $a;

                $amenity->save();
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

            // $payment = new Payment;

            // $payment->dorm_id = $dorm->id
            // $payment->range_from = $request->range_from;
            // $payment->range_to = $request->range_to;
            // $payment->methods = implode(',', $request->payments);

            // $payment->save();

            return response()->json(['message' => 'Your dorm succesfully registered.', 'status' => 200], 200);

        }

        return response()->json(['message' => 'Error creating dorm.', 'status' => 500], 200);
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

        $applications = TenantRoom::with('payments')->where('owner_id', $user->id);

        $currentMonth = Carbon::now()->month;
        $paidAmount = 0;
        $unpaidAmount = 0;

        foreach($applications->where('is_approved', true)->where('is_active', true)->get() as $application) {
            $application = (object) $application;

            foreach($application->payments as $payment) {
                $paymentMonth = Carbon::parse($payment->date)->month;

                if($currentMonth == $paymentMonth) {
                    if($payment->is_paid) {
                        $paidAmount += $payment->amount_to_pay;
                    } else {
                        $balance = $payment->amount_paid != null ? $payment->amount_to_pay - $payment->amount_paid : $payment->amount_to_pay;
                        $unpaidAmount += $balance;
                    }
                }
            }
        }

        return Inertia::render('Owner/Dashboard', [
            'paidAmount' => $paidAmount,
            'unpaidAmount' => $unpaidAmount,
            'totalTenants' => $applications->where('is_approved', true)->where('is_active', true)->count(),
            'totalApplications' => $applications->count()
        ]);
    }

    public function reports()
    {
        return Inertia::render('Owner/Report', [

        ]);
    }

    public function billings()
    {
        return Inertia::render('Owner/Billings', [

        ]);
    }
}
