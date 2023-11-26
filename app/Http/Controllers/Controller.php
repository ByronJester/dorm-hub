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

    public function createInvoice($pk = null, $sk = null)
    {
        if(!$pk) {
            $pk = 'xnd_public_development_tiwVzngSwy8AvuyaHGAiKYi8SIr1pBNmTJMEV+vgl67p9pOO0LSVAN0XNE6EeFP3';
        }

        if(!$sk) {
            $sk = 'xnd_development_o4VmcGFOZtqTqNst1ckDFEebZV39jZX02nKxu91Wg3Z0aLqmMNULPJCiEzrZgEc';
        }

        Xendit::setApiKey($sk);
        $version = Xendit::getVersion();
        return response()->json($version);

        $externalId = Str::random(15); // Unique ID for reference
        $amount = 100000; // Payment amount in IDR (Indonesian Rupiah)
        $bankCode = 'MANDIRI'; // Bank code (example: Mandiri)

        try {
            $payment = \Xendit\Invoice::create([
                'external_id' => $externalId,
                'amount' => $amount,
                'payer_email' => 'payer@example.com',
                'description' => 'Bank Payment',
                'bank_code' => $bankCode,
                'payment_method' => 'BankTransfer', // Specify bank transfer as payment method
            ]);

            // Handle successful payment response
            return response()->json($payment);
        } catch (\Xendit\Exceptions\ApiException $e) {
            // Handle API exception (e.g., failed payment)
            return response()->json($e->getMessage());
        }
    }


}
