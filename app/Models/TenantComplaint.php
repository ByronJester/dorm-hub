<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TenantComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject', 'complain', 'tenant_id', 'status', 'profile_id'
    ];

    protected $appends = [
        'display_created_at', 'display_updated_at'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getDisplayCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->isoFormat('LL');
    }

    public function getDisplayUpdatedAtAttribute()
    {
        if($this->status == 'finish') {
            return Carbon::parse($this->updated_at)->isoFormat('LL');
        }

        return null;

    }
}
