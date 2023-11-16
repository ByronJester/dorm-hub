<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackUp extends Model
{
    use HasFactory;
    protected $table = 'backup_and_restore';

    protected $fillable = [
        'user_id',
        'operation_type',
        'operation_date',
        // Add other fillable columns as needed
    ];
}
