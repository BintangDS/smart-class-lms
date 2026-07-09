<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['module_id', 'title', 'passing_score'];

    /**
     * Get the module that owns the quiz.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the questions for this quiz.
     */
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    /**
     * Get the attempts made for this quiz.
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
