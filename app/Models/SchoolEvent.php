<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolEvent extends Model
{
    protected $fillable = [
        'academic_year_id',
        'title',
        'description',
        'event_date',
        'type' // holiday, meeting, exam, event, recess
    ];
}
