<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SiteUser extends Pivot
{
    protected $table = 'site_user';

    protected $fillable = [
        'user_id',
        'site_id',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];
}
