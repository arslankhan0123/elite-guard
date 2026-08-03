<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportGeneralForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'site_id',
        'report_date',
        'report_time',
        'property_location',
        'property_name',
        'property',
        'property_address',
        'reported_by',
        'reported_by_id',
        'report_type',
        'time_engaged',
        'time_area_cleared',
        'location_of_incident',
        'location',
        'location_of_report',
        'observation_situation',
        'action_taken',
        'signature',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function images()
    {
        return $this->hasMany(ReportGeneralFormImage::class);
    }
}
