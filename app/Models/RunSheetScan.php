<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunSheetScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'run_sheet_id',
        'nfc_tag_id',
        'user_id',
        'date',
        'time',
        'latitude',
        'longitude',
    ];

    /**
     * Get the run sheet associated with the scan.
     */
    public function runSheet()
    {
        return $this->belongsTo(RunSheet::class);
    }

    /**
     * Get the NFC tag associated with the scan.
     */
    public function nfcTag()
    {
        return $this->belongsTo(NfcTag::class);
    }

    /**
     * Get the user who performed the scan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
