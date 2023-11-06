<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantBilling extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'tenant_application_id', 'first_name', 'last_name', 'phone_number', 'amount',
        'description', 'date', 'is_paid', 'subject'
    ];

    protected $appends = [
        'invoice_no'
    ];

    public function getInvoiceNoAttribute()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $invoice = '';

        $max = strlen($characters) - 1;

        for ($i = 0; $i < 8; $i++) {
            $invoice .= $characters[random_int(0, $max)];
        }

        return 'INV-' . $invoice;
    }
}
