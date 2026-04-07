<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeClock extends Model
{
    protected $fillable = [
        'user_id',
        'nfc_tag_id',
        'check_in_time',
        'check_out_time',
        'status',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nfcTag()
    {
        return $this->belongsTo(NfcTag::class);
    }
}
