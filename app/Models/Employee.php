<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'cnic',
        'gender',
        'joining_date',
        'status',
        'is_email_sent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
