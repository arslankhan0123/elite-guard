<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOfferLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_title',
        'joining_date',
        'salary',
        'description',
        'is_email_sent',
        'is_accepted',
        'signed_at',
        'signature'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
