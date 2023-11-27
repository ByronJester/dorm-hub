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

    public function create(float $amount, string $description, $currency = 'PHP' )
    {
        $response = null;

        try {
            $date = new \DateTime();

            $payload = [
                'amount' => $amount,
                'invoice_duration' => 172800,
                'external_id' => 'INV-' . $date->getTimestamp(),
                'description' => $description,
                'currency' => $currency,
                'reminder_time' => 1
            ];

            $response = $this->invoiceApi->createInvoice($payload);
        } catch (Exception $e){
            return response()->json($e->getMessage());
        }

        return $response;
    }

}
