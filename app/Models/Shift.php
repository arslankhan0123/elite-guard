<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'site_id',
        'date',
        'shift_name',
        'start_time',
        'end_time',
    ];

    /**
     * Get the schedule that owns the shift.
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the site assigned in this shift.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
