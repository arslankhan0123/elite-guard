<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyVehicleChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time',
        'vehicle',
        'odometer_reading',
        'fuel',
        'assigned_site',
        'driver',
        'signature',
        'cosmetic_issues',
        'tires',
        'windows',
        'staff_care',
        'dash_lights_gauges',
        'documents',
        'engine',
        'oil_life_percentage',
        'equipment',
        'bwc_used_for_inspection',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
