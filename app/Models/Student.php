<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = ['name', 'registration_number', 'birth_date'];

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
