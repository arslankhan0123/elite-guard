<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLicenseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'security_license_number',
        'security_license_expiry',
        'security_license_file',
        'drivers_license_number',
        'drivers_license_expiry',
        'drivers_license_file',
        'work_eligibility_type_number',
        'work_eligibility_expiry',
        'work_eligibility_file',
        'criminal_record_check',
        'first_aid_training',
        'other_certificates',
        'other_documents_file',
    ];

    protected $casts = [
        'security_license_file' => 'array',
        'drivers_license_file' => 'array',
        'work_eligibility_file' => 'array',
        'other_documents_file' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
