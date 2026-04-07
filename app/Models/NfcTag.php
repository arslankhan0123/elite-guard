<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfcTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'uid',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function timeClocks()
    {
        return $this->hasMany(TimeClock::class, 'nfc_tag_id');
    }
}
