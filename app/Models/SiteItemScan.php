<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteItemScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_item_id',
        'nfc_tag_id',
        'user_id',
        'site_id',
        'date',
        'time',
        'image',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function siteItem()
    {
        return $this->belongsTo(SiteItem::class);
    }

    public function nfcTag()
    {
        return $this->belongsTo(NfcTag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
