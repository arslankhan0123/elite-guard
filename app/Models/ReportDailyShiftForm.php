<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDailyShiftForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_id',
        'security_company',
        'security_guard',
        'date',
        'shift_time',
        'location',
        'client',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function patrolEntries()
    {
        return $this->hasMany(ReportDailyShiftFormPatrolEntry::class);
    }
}
