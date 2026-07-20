<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteTourItemScan extends Model
{
    protected $fillable = [
        'site_tour_item_id',
        'nfc_tag_id',
        'site_id',
        'user_id',
        'date',
        'time',
        'image',
    ];
}
