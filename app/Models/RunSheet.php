<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'site_id',
        'shift_id',
        'weekly_run_sheet_entry_id',
        'date',
        'run_sheet_name',
        'start_time',
        'end_time',
        'duration',
        'job_type',
        'sequence',
        'runsheet_status',
    ];

    /**
     * Get the user that owns the run sheet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the site assigned in this run sheet.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the shift assigned in this run sheet.
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Get the weekly run sheet entry associated with this run sheet.
     */
    public function weeklyRunSheetEntry()
    {
        return $this->belongsTo(WeeklyRunSheetEntry::class, 'weekly_run_sheet_entry_id');
    }

    /**
     * Get the scans recorded for this run sheet.
     */
    public function scans()
    {
        return $this->hasMany(RunSheetScan::class);
    }
}
