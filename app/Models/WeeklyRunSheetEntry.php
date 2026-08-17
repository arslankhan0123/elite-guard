<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyRunSheetEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'day_of_week',
        'tour_name',
        'start_time',
        'end_time',
        'sequence',
    ];

    public function runSheet()
    {
        return $this->belongsTo(WeeklyRunSheet::class, 'weekly_run_sheet_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function scans()
    {
        return $this->hasMany(WeeklyRunSheetScan::class, 'weekly_run_sheet_entry_id');
    }
}
