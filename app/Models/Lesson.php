<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'title',
        'content_type',
        'content',
        'order',
    ];

    /**
     * Get the module that owns the lesson.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the progress records for this lesson.
     */
    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }
}
