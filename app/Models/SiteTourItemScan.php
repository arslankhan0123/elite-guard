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

    public function nfcTag()
    {
        return $this->belongsTo(NfcTag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function siteTourItem()
    {
        return $this->belongsTo(SiteTourItem::class);
    }
}
