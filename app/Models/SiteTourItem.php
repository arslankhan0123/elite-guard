<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteTourItem extends Model
{
    protected $fillable = [
        'site_tour_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'status',
        'user_id',
        'site_id',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => 'boolean',
    ];

    public function siteTour()
    {
        return $this->belongsTo(SiteTour::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function scans()
    {
        return $this->hasMany(SiteTourItemScan::class, 'site_tour_item_id');
    }
}
