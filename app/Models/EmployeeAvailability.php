<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'availability_date',
        'willing_hours',
        'unable_hours',
        'unable_days',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
