<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignedPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'policy_id',
        'agreed',
        'document',
    ];

    /**
     * Get the user that signed the policy.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the policy that was signed.
     */
    public function policy()
    {
        return $this->belongsTo(Policy::class);
    }
}
