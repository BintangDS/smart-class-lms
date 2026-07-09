<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
        'progress_percent',
        'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that enrolled.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course enrolled in.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the completed lessons for this enrollment.
     */
    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }
}
