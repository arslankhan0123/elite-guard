<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class TenantUserLookup extends Model
{
    protected $connection = 'master';
    protected $table      = 'tenant_user_lookup';

    protected $fillable = [
        'tenant_id',
        'email',
        'user_id',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
