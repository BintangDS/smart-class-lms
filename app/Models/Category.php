<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Get the courses under this category.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
