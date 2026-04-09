<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOfficeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employment_type',
        'start_date',
        'job_position',
        'wage',
        'other_notes',
        'hiring_manager_name',
        'hiring_manager_signature',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
