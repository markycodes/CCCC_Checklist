<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'student_id',
    'name',
    'entry_year',
    'expected_graduation_year',
    'previous_school',
    'last_year_attended',
])]
class Student extends Model
{
    protected function casts(): array
    {
        return [
            'entry_year' => 'integer',
            'expected_graduation_year' => 'integer',
        ];
    }

    public function programHead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function completedCourses(): HasMany
    {
        return $this->hasMany(StudentCourseCompletion::class);
    }
}
