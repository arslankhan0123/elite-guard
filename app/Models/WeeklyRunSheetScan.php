<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyRunSheetScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_run_sheet_id',
        'weekly_run_sheet_entry_id',
        'nfc_tag_id',
        'user_id',
        'date',
        'time',
        'latitude',
        'longitude',
        'image',
        'reason',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function weeklyRunSheet()
    {
        return $this->belongsTo(WeeklyRunSheet::class);
    }

    public function weeklyRunSheetEntry()
    {
        return $this->belongsTo(WeeklyRunSheetEntry::class);
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
