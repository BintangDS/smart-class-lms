<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'level',
        'status',
    ];

    /**
     * Get the instructor of this course.
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the category of this course.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the modules for this course.
     */
    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order', 'asc');
    }

    /**
     * Get the enrollments for this course.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
