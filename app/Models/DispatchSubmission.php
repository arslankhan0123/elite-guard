<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchSubmission extends Model
{
    protected $fillable = [
        'dispatch_id',
        'user_id',
        'file_attachment',
        'action_taken',
    ];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
