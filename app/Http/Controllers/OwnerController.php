<?php

namespace App\Http\Controllers;


use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\{ Dorm, Room, Amenity, Rule, Payment };
use App\Http\Requests\{ SaveDorm };

class OwnerController extends Controller
{
    public function dormList()
    {
        return Inertia::render('Owner/Dorms');
    }

    public function saveDorm(Request $request)
    {
        $aaa= \LaravelCloudinary::show('text', []);
        return $aaa;

        $validator = Validator::make($request->all(), [
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
            // 'rooms' => 'required|array|between:1,20',
            // 'amenities' => 'required|array|between:1,8',
            'short_term' => 'required',
            'mix_gender' => 'required',
            'curfew' => 'required',
            'curfew_hours' => 'required',
            'minimum_stay' => 'required',
            // 'rules' => 'required|array|between:1,20',
            'deposit' => 'required',
            'advance' => 'required',
            'fee' => 'required',
            'payments' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 422);
        }

        $dorm = null;

        if($id = $request->id) {
            $dorm = Dorm::where('id', $id)->first();

            if($dorm) {
                Room::where('dorm_id', $id)->delete();
                Amenity::where('dorm_id', $id)->delete();
                Rule::where('dorm_id', $id)->delete();
                Payment::where('dorm_id', $id)->delete();
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

            $filename = Str::random(10) . '_dorm_image.' . $dorm_image->getClientOriginalExtension();
            $dorm->dorm_image = $filename;

            // Storage::disk('cloudinary')->put($filename, $dorm_image);
            \LaravelCloudinary::upload($dorm_image, 'text', [], []);
        }

        if($business_permit_image = $request->business_permit_image) {
            $business_permit_image = $request->business_permit_image;

            $filename = Str::random(10) . '_business_permit.' . $business_permit_image->getClientOriginalExtension();
            $dorm->business_permit_image = $filename;

            // Storage::put($filename, $business_permit_image);
        }

        if($dorm->save()) {
            $rooms = json_decode($request->rooms);

            foreach($rooms as $key => $r) {
                // $r = json_decode($r);

                $room_image = $request->room_images[$key];

                $filename = Str::random(10) . '_room_image.' . $room_image->getClientOriginalExtension();

                $room = new Room;

                $room->dorm_id = $dorm->id;
                $room->type_of_room = $r->type_of_room;
                $room->is_aircon = $r->is_aircon;
                $room->furnished_type = $r->furnished_type;
                $room->is_available = $r->is_available;
                $room->image = $filename;

                // Storage::put($filename, $room_image);

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

            $payment = new Payment;

            $payment->dorm_id = $dorm->id;
            $payment->deposit = $request->deposit;
            $payment->advance = $request->advance;
            $payment->fee = $request->fee;
            $payment->methods = implode(',', $request->payments);

            $payment->save();

            return response()->json(['message' => 'Your dorm succesfully registered.', 'status' => 200], 200);

        }

        return response()->json(['message' => 'Error creating dorm.', 'status' => 500], 200);
    }
}
