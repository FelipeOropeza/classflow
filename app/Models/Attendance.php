<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'enrollment_id',
        'class_diary_id',
        'status',
        'observation'
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function classDiary(): BelongsTo
    {
        return $this->belongsTo(ClassDiary::class);
    }
}
