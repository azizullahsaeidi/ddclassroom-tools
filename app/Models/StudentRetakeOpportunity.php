<?php

namespace App\Models;

use App\Models\Relations\BelongsToStudent;
use App\Models\Relations\BelongsToSubGrade;
use App\Models\Relations\BelongsToSubject;
use App\Models\Relations\BelongsToYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class StudentRetakeOpportunity extends Model
{
    use HasFactory, BelongsToStudent, BelongsToSubGrade, BelongsToSubject, BelongsToYear;

    protected $fillable = [
        'student_id',
        'sub_grade_id',
        'year_id',
        'subject_id',
        'score',
        'second_chance_score',
        'third_chance_score',
        'second_chance_date',
        'third_chance_date',
        'is_passed',
    ];

    /**
     * Scope to apply filters from request
     */
    public function scopeFilter(Builder $query, Request|array $filters): Builder
    {
        $request = is_array($filters) ? new Request($filters) : $filters;

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->whereAny(['name', 'fa_name', 'email', 'id_number'], 'like', "%$search%");
            });
        }

        if ($request->sub_grade_id) {
            $query->where('sub_grade_id', $request->sub_grade_id);
        }

        if ($request->year_id) {
            $query->where('year_id', $request->year_id);
        }

        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('is_passed') && $request->is_passed !== '') {
            $query->where('is_passed', $request->boolean('is_passed'));
        }

        return $query;
    }
}
