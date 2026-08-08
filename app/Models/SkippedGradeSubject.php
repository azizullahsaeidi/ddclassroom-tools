<?php

namespace App\Models;

use App\Models\Relations\BelongsToSubGrade;
use App\Models\Relations\BelongsToSubject;
use App\Models\Relations\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkippedGradeSubject extends Model
{
    use HasFactory, BelongsToSubGrade, BelongsToSubject, BelongsToUser;

    protected $fillable = [
        'sub_grade_id',
        'year',
        'subject_id',
        'user_id',
        'note',
    ];
}
