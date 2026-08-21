<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyRunSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'week_start_date',
        'notes',
        'monday_start_time',
        'monday_end_time',
        'tuesday_start_time',
        'tuesday_end_time',
        'wednesday_start_time',
        'wednesday_end_time',
        'thursday_start_time',
        'thursday_end_time',
        'friday_start_time',
        'friday_end_time',
        'saturday_start_time',
        'saturday_end_time',
        'sunday_start_time',
        'sunday_end_time',
    ];

    protected $casts = [
        'week_start_date' => 'date',
    ];

    public function getDayStartTime(int $dayNumber): ?string
    {
        $map = [
            1 => 'monday_start_time',
            2 => 'tuesday_start_time',
            3 => 'wednesday_start_time',
            4 => 'thursday_start_time',
            5 => 'friday_start_time',
            6 => 'saturday_start_time',
            7 => 'sunday_start_time',
        ];
        $attr = $map[$dayNumber] ?? null;
        return $attr ? $this->$attr : null;
    }

    public function getDayEndTime(int $dayNumber): ?string
    {
        $map = [
            1 => 'monday_end_time',
            2 => 'tuesday_end_time',
            3 => 'wednesday_end_time',
            4 => 'thursday_end_time',
            5 => 'friday_end_time',
            6 => 'saturday_end_time',
            7 => 'sunday_end_time',
        ];
        $attr = $map[$dayNumber] ?? null;
        return $attr ? $this->$attr : null;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['day_times'] = [
            1 => ['start_time' => $this->monday_start_time, 'end_time' => $this->monday_end_time],
            2 => ['start_time' => $this->tuesday_start_time, 'end_time' => $this->tuesday_end_time],
            3 => ['start_time' => $this->wednesday_start_time, 'end_time' => $this->wednesday_end_time],
            4 => ['start_time' => $this->thursday_start_time, 'end_time' => $this->thursday_end_time],
            5 => ['start_time' => $this->friday_start_time, 'end_time' => $this->friday_end_time],
            6 => ['start_time' => $this->saturday_start_time, 'end_time' => $this->saturday_end_time],
            7 => ['start_time' => $this->sunday_start_time, 'end_time' => $this->sunday_end_time],
        ];
        return $array;
    }

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
