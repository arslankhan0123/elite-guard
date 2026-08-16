<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'start_time',
        'status',
        'type',
        'user_id',
        'reason',
        'end_time',
        'date',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'status' => 'boolean',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scans()
    {
        return $this->hasMany(SiteItemScan::class);
    }
}
