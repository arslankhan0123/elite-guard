<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FireWatchPatrolLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'fire_watch_report_id',
        'round',
        'date',
        'start_time',
        'end_time',
        'area_patrolled_findings',
        'initials',
    ];

    public function fireWatchReport()
    {
        return $this->belongsTo(FireWatchReport::class);
    }
}
