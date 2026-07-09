<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'user_id', 'score', 'submitted_at'];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the quiz that was attempted.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the user who made the attempt.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
