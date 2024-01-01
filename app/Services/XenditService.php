<?php

namespace App\Services;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\BalanceAndTransaction\TransactionApi;

class XenditService {
    protected $apiInstance;
    protected $invoiceApi;

    public function __construct($sk)
    {
        Configuration::setXenditKey($sk);
        $this->transactionApi = new TransactionApi;
        $this->invoiceApi = new InvoiceApi;
    }

    public function get($reference_id, $currency = 'PHP')
    {
        $response = $this->transactionApi->getAllTransactions(
            $for_user_id = null,
            $types = null,
            $statuses = null,
            $channel_categories = null,
            $reference_id = $reference_id, //'INV-1700979640'
            $product_id = null,
            $account_identifier = null,
            $amount = null,
            $currency = $currency,
            $created = null,
            $updated = null,
            $limit = null,
            $after_id = null,
            $before_id = null
        );

        return $response;
    }

    public function create($amount, $description, $action, $currency = 'PHP')
    {
        $response = null;
        $date = new \DateTime();
        $successRoute = null;
        $invoice = 'INV-' . $this->generateInvoiceNumber();

        if($action == 'addDorm') {
            $successRoute = route('add-dorm.success', $invoice);
        }

        if($action == 'tenantPayment') {
            $successRoute = route('tenant-payment.success', $invoice);
        }

        if($action == 'updateSubscription') {
            $successRoute = route('update-subscription.success', $invoice);
        }

        try {
            $payload = [
                'amount' => $amount,
                'invoice_duration' => 172800,
                'external_id' => $invoice,
                'description' => $description,
                'currency' => $currency,
                'reminder_time' => 1,
                'failure_redirect_url' => route('payment.fail'),
                'success_redirect_url' => $successRoute
            ];

            $response = $this->invoiceApi->createInvoice($payload);
        } catch (Exception $e){
            // return response()->json($e->getMessage());
            $response = null;
        }

        return $response;
    }

    public function generateInvoiceNumber($length = 6) {
        $characters = '0123456789';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

}
