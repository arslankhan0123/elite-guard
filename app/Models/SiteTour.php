<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteTour extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'name',
        'description',
        'scheduled_days',
        'is_continuous',
        'schedule_type',
        'specific_times',
        'max_duration',
        'tag_type',
        'tags',
        'assigned_guards',
    ];

    protected $casts = [
        'scheduled_days' => 'array',
        'specific_times' => 'array',
        'tags' => 'array',
        'max_duration' => 'array',
        'assigned_guards' => 'array',
        'is_continuous' => 'boolean',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
