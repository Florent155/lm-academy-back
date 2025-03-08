<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'nr_of_files',
        'duration'
    ];

     public function course() {
        return $this->belongsTo(Course::class, 'course_id');

     }

}
