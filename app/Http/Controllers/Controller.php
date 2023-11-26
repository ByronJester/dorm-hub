<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use Carbon\Carbon;
use Xendit\Xendit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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

    public function createInvoice($request, $pk = null, $sk = null)
    {
        if(!$pk) {
            $pk = 'xnd_public_development_tuyYcyfN3JWXFRdUX56GfEfubkcs25ERvUNos9yzA1HqlohquJqebcUq9bsdrEyd+vgl67p9pOO0LSVAN0XNE6EeFP3';
        }

        if(!$sk) {
            $sk = 'xnd_development_2hh1kPCMyT6d7sHYBRItuUTcP3v1ukfXAHz6WKBjosbZkR0RtLtxeZTw2TPaX5Zr';
        }

        Xendit::setApiKey($sk);

        $reference = substr(uniqid(), 1, 13);
        $amount = 100000; // Payment amount in IDR (Indonesian Rupiah)

        try {
            // $payment = \Xendit\EWallets::createEWalletCharge([
            //     'external_id' => $externalId,
            //     'amount' => $amount, // Payment amount in IDR
            //     'phone' => '09771023141',
            //     'ewallet_type' => 'GCASH', // E-wallet type (e.g., OVO, GoPay, DANA)
            //     'callback_url' => 'https://dormhub.onrender.com', // Optional callback URL
            //     // Additional parameters based on Xendit's API
            // ]);

            $params = $this->setupParameters($amount, $reference);

            $payment = \Xendit\EWallets::createEWalletCharge($params);

            // Handle successful payment response
            return response()->json($payment);
        } catch (\Xendit\Exceptions\ApiException $e) {
            // Handle API exception (e.g., failed payment)
            return response()->json($e->getMessage());
        }
    }


    public function setupParameters(float $amount, string $reference) : array
    {
        $amount  = (float) number_format($amount, 2);
        $reference = substr(uniqid(), 1, 13);

        return [
            'reference_id' => $reference,
            'currency' => 'PHP',
            'amount' => $amount,
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => 'PH_GCASH',
            'channel_properties' => [
                'success_redirect_url' => route('payment.success') . "?reference_id={$reference}",
                'failure_redirect_url' => route('payment.fail')
            ],
            'metadata' => [
                'branch_code' => 'tree_branch'
            ]
        ];
    }


}
