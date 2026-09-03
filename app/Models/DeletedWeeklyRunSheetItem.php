<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedWeeklyRunSheetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_run_sheet_entry_id',
        'date',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function weeklyRunSheetEntry()
    {
        return $this->belongsTo(WeeklyRunSheetEntry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
