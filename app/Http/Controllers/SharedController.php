<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\{ User, Dorm, Room, Notification, Thread, ThreadMessage, TenantRoom, TenantPayments };
use Illuminate\Support\Facades\Auth;

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
        $dorm = Dorm::where('id', $dorm_id)->first();

        return Inertia::render('Dorm', [
            'dorm' => $dorm
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
}
