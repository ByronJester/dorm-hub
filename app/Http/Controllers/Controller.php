<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use Carbon\Carbon;
// use Xendit\Xendit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\BalanceAndTransaction\TransactionApi;

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

    // public function createInvoice($request, $pk = null, $sk = null)
    // {
    //     if(!$pk) {
    //         $pk = 'xnd_public_development_tuyYcyfN3JWXFRdUX56GfEfubkcs25ERvUNos9yzA1HqlohquJqebcUq9bsdrEyd+vgl67p9pOO0LSVAN0XNE6EeFP3';
    //     }

    //     if(!$sk) {
    //         $sk = 'xnd_development_2hh1kPCMyT6d7sHYBRItuUTcP3v1ukfXAHz6WKBjosbZkR0RtLtxeZTw2TPaX5Zr';
    //     }

    //     // Xendit::setApiKey($sk);
    //     Configuration::setXenditKey($sk);

    //     $reference = substr(uniqid(), 1, 13);
    //     $amount = 100000; // Payment amount in IDR (Indonesian Rupiah)

    //     try {
    //         // $date = new \DateTime();
    //         // $redirectUrl = '';
    //         // $params = [
    //         //     'external_id' => 'INV-' . $date->getTimestamp(),
    //         //     'payer_email' => 'byronjester.manalo@gmail.com',
    //         //     'description' => 'Test Xendit',
    //         //     'amount' => $amount,
                // 'failure_redirect_url' => route('payment.success'),
                // 'success_redirect_url' => route('payment.fail')
    //         // ];

    //         // $response = \Xendit\Invoice::create($params);

    //         // return response()->json($response);

    //         // $invoices = \Xendit\Invoice::retrieveAll([
    //         //     // Optional parameters for filtering or pagination
    //         //     'external_id' => 'INV-1700979640',
    //         // ]);

    //         // return response()->json($invoices);

    //         // $detail = \Xendit\Transaction::detail('txn_1252f849-2949-42ea-b7fc-6abef1756308');
    //         // return response()->json($detail);

    //         // $apiInstance = new TransactionApi();

    //         // $result = $apiInstance->getAllTransactions($for_user_id = null, $types = null, $statuses = null, $channel_categories = null, $reference_id = 'INV-1700979640', $product_id = null, $account_identifier = null, $amount = null, $currency = 'PHP', $created = null, $updated = null, $limit = null, $after_id = null, $before_id = null);

    //         // return response()->json($result);

    //         // $apiInstance = new InvoiceApi();
    //         // $response = null;
    //         // try {
    //         //     $payload = [
    //         //         'amount' => 10000,
    //         //         'invoice_duration' => 172800,
    //         //         'external_id' => getenv('BUSINESS_ID') . "_" .time(),
    //         //         'description' => 'Test Invoice',
    //         //         'currency' => 'PHP',
    //         //         'reminder_time' => 1
    //         //     ];

    //         //     $response = $apiInstance->createInvoice($payload);
    //         // } catch (Exception $e){
    //         //     return response()->json($e->getMessage());
    //         // }

    //         // return response()->json($response);




    //     } catch (\Xendit\Exceptions\ApiException $e) {
    //         // Handle API exception (e.g., failed payment)
    //         return response()->json($e->getMessage());
    //     }
    // }


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
                // 'success_redirect_url' => route('payment.success') . "?reference_id={$reference}",
                'success_redirect_url' => route('payment.success'),
                'failure_redirect_url' => route('payment.fail')
            ],
            'metadata' => [
                'branch_code' => 'tree_branch'
            ]
        ];
    }

}
