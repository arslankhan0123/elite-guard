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
        'date_of_incident',
        'time_of_incident',
        'location',
        'property',
        'property_name',
        'property_location',
        'incident_location',
        'incident_type',
        'reported_by',
        'reported_by_id',
        'reporting_guard_name',
        'employee_id',
        'responding_authority',
        'responding_authority_case_number',
        'supervisor_notified',
        'cps_case_number',
        'incident_report',
        'action_taken',
        'evidence_observed',
        'subjects',
        'subject_description',
        'outcome',
        'reported_by_name',
        'reported_by_title',
        'reviewed_by_name',
        'reviewed_by_title',
    ];

    protected function casts(): array
    {
        return [
            'subjects' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(ReportIncidentFormImage::class);
    }
}
