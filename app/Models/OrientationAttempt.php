<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrientationAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'orientation_id',
        'score',
        'is_passed',
        'answers',
    ];

    protected $casts = [
        'answers' => 'array',
        'is_passed' => 'boolean',
        'score' => 'decimal:2',
    ];

    /**
     * Get the user that made the attempt.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orientation being attempted.
     */
    public function orientation()
    {
        return $this->belongsTo(Orientation::class);
    }
}
