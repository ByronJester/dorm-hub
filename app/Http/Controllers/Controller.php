<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use Carbon\Carbon;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function uploadFile($file, $identifier)
    {
        $options = [

        ];

        $tags = [

        ];

        \LaravelCloudinary::upload($file, $identifier, $options, $tags);

        return $identifier;
    }

    public function sendSMS($number, $message)
    {
        $ch = curl_init();

        $parameters = array(
            'apikey' =>  base64_decode("OWVlZTYyNzM1ZWFhNmE1OGQ1ZjY2YzQ2M2M3YzE3NGI="),
            'number' => $number,
            'message' => $message,
            'sendername' => 'SEMAPHORE'
        );

        curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
        curl_setopt( $ch, CURLOPT_POST, 1 );

        //Send the parameters set above with the request
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

        // Receive response from server
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close ($ch);
    }

    public function generateInvoice($user_id)
    {
        $user = User::where('id', $user_id)->first();

        $date = Carbon::parse($user->created_at);

        $timestamp = $date->timestamp;

        return "INVOICE-$timestamp";
    }
}
