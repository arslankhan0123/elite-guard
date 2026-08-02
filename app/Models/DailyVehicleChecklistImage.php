<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyVehicleChecklistImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_vehicle_checklist_id',
        'image_path',
    ];

    public function checklist()
    {
        return $this->belongsTo(DailyVehicleChecklist::class, 'daily_vehicle_checklist_id');
    }
}
