<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'site_id',
        'week_start_date',
        'notes',
    ];

    /**
     * Get the user assigned to this schedule.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the site assigned in this schedule.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
