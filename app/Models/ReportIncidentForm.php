<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportIncidentForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_of_report',
        'time_of_report',
        'location',
        'property',
        'incident_type',
        'reported_by',
        'responding_authority',
        'cps_case_number',
        'incident_report',
        'subject_description',
        'outcome',
        'reported_by_name',
        'reported_by_title',
        'reviewed_by_name',
        'reviewed_by_title',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(ReportIncidentFormImage::class);
    }
}
