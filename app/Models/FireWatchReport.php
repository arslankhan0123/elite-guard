<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FireWatchReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'site_id',
        'supervisor_id',
        'client_site_name',
        'address_location',
        'reason_for_fire_watch',
        'fire_watch_areas',
        'commenced_date',
        'commenced_time',
        'terminated_date',
        'terminated_time',
        'guards',
        'supervisor',
        'patrol_interval',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function supervisorUser()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function patrolLogs()
    {
        return $this->hasMany(FireWatchPatrolLog::class);
    }
}
