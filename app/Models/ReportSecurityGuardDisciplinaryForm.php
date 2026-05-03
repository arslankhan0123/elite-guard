<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSecurityGuardDisciplinaryForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_name',
        'employee_id_license',
        'site_property',
        'warning_date',
        'supervisor',
        'shift_time',
        'department_client',
        'reference_number',
        'violation_type',
        'classification_severity',
        'classification_severity_other',
        'incident_date',
        'incident_time',
        'location',
        'reported_by',
        'incident_summary',
        'corrective_action',
        'action_taken',
        'issued_by',
        'issued_by_title',
        'employee_signature',
        'signature_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
