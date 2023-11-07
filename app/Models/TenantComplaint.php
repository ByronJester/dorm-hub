<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject', 'complain', 'tenant_application_id'
    ];
}
