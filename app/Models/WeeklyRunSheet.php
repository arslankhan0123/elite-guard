<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyRunSheet extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'week_start_date', 'notes'];

    protected $casts = ['week_start_date' => 'date'];

    public function entries()
    {
        return $this->hasMany(WeeklyRunSheetEntry::class)
            ->orderBy('day_of_week')
            ->orderBy('sequence')
            ->orderBy('start_time');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_weekly_run_sheet')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function scans()
    {
        return $this->hasMany(WeeklyRunSheetScan::class);
    }
}
