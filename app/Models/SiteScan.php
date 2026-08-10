<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'nfc_tag_id',
        'user_id',
        'date',
        'time',
        'image',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function nfcTag()
    {
        return $this->belongsTo(NfcTag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
