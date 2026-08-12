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
        'reason',
        'user_id',
        'site_id',
    ];

    protected $casts = [
        // Tour items represent a calendar day, not a moment in time. Keeping the
        // serialized value date-only prevents midnight in the app timezone from
        // being converted to the previous UTC date in JSON responses.
        'date' => 'date:Y-m-d',
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
