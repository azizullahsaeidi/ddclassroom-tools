<?php

namespace App\Http\Controllers;

use App\Models\SkippedGradeSubject;
use App\Models\SubGrade;
use App\Models\Subject;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkippedGradeSubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = SkippedGradeSubject::query()
            ->with([
                'subGrade:id,name,full_name',
                'subject:id,name,en_name',
                'user:id,name',
            ]);

        if ($request->sub_grade_id) {
            $query->where(
                'sub_grade_id',
                $request->sub_grade_id
            );
        }

        if ($request->subject_id) {
            $query->where(
                'subject_id',
                $request->subject_id
            );
        }

        if ($request->year) {
            $query->where(
                'year',
                $request->year
            );
        }

        $skippedGradeSubjects = $query
            ->orderBy('year', 'desc')
            ->orderBy('sub_grade_id')
            ->orderBy('subject_id')
            ->paginate(50)
            ->withQueryString();


        $subGrades = SubGrade::query()
            ->orderBy('full_name')
            ->get([
                'id',
                'name',
                'full_name',
            ]);

        $subjects = Subject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'en_name',
            ]);

        $years = Year::query()
            ->orderBy('name', 'desc')
            ->get([
                'id',
                'name',
            ]);

        return inertia('SkippedGradeSubject/Index', [
            'skippedGradeSubjects' => $skippedGradeSubjects,
            'subGrades' => $subGrades,
            'subjects' => $subjects,
            'years' => $years,

            'filters' => [
                'sub_grade_id' => $request->sub_grade_id,
                'subject_id' => $request->subject_id,
                'year' => $request->year,
            ],
        ]);
    }

    public function create()
    {
        $subGrades = SubGrade::query()
            ->orderBy('full_name')
            ->get([
                'id',
                'name',
                'full_name',
            ]);

        $subjects = Subject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'en_name',
            ]);

        $years = Year::query()
            ->orderBy('name', 'desc')
            ->get([
                'id',
                'name',
            ]);

        return inertia('SkippedGradeSubject/Create', [
            'subGrades' => $subGrades,
            'subjects' => $subjects,
            'years' => $years,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_grade_id' => [
                'required',
                'exists:sub_grades,id',
            ],

            'year' => [
                'required',
                'integer',
            ],

            'subject_ids' => [
                'nullable',
                'array',
            ],

            'subject_ids.*' => [
                'integer',
                'distinct',
                'exists:subjects,id',
            ],

            'note' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $subGradeId = $validated['sub_grade_id'];
        $year = $validated['year'];
        $subjectIds = $validated['subject_ids'] ?? [];

        DB::transaction(function () use (
            $subGradeId,
            $year,
            $subjectIds,
            $validated
        ) {

            SkippedGradeSubject::query()
                ->where('sub_grade_id', $subGradeId)
                ->where('year', $year)
                ->delete();

            foreach ($subjectIds as $subjectId) {
                SkippedGradeSubject::create([
                    'sub_grade_id' => $subGradeId,
                    'year' => $year,
                    'subject_id' => $subjectId,
                    'user_id' => auth()->id(),
                    'note' => $validated['note'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('skipped-grade-subjects.index')
            ->with(
                'success',
                'Skipped subjects saved successfully.'
            );
    }

    public function edit(SkippedGradeSubject $skippedGradeSubject)
    {
        $subGrades = SubGrade::query()
            ->orderBy('full_name')
            ->get([
                'id',
                'name',
                'full_name',
            ]);

        $subjects = Subject::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'en_name',
            ]);

        $years = Year::query()
            ->orderBy('name', 'desc')
            ->get([
                'id',
                'name',
            ]);

        $selectedSubjectIds = SkippedGradeSubject::query()
            ->where(
                'sub_grade_id',
                $skippedGradeSubject->sub_grade_id
            )
            ->where(
                'year',
                $skippedGradeSubject->year
            )
            ->pluck('subject_id')
            ->map(fn ($id) => (int) $id)
            ->values();

        return inertia('SkippedGradeSubject/Edit', [
            'skippedGradeSubject' => [
                'id' => $skippedGradeSubject->id,

                'sub_grade_id' =>
                    $skippedGradeSubject->sub_grade_id,

                'year' =>
                    $skippedGradeSubject->year,

                'note' =>
                    $skippedGradeSubject->note,

                'subject_ids' =>
                    $selectedSubjectIds,
            ],

            'subGrades' => $subGrades,
            'subjects' => $subjects,
            'years' => $years,
        ]);
    }

    public function update(
        Request $request,
        SkippedGradeSubject $skippedGradeSubject
    ) {
        $validated = $request->validate([
            'subject_ids' => [
                'nullable',
                'array',
            ],

            'subject_ids.*' => [
                'integer',
                'distinct',
                'exists:subjects,id',
            ],

            'note' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $subGradeId = $skippedGradeSubject->sub_grade_id;
        $year = $skippedGradeSubject->year;

        $subjectIds = $validated['subject_ids'] ?? [];

        DB::transaction(function () use (
            $subGradeId,
            $year,
            $subjectIds,
            $validated
        ) {
            SkippedGradeSubject::query()
                ->where('sub_grade_id', $subGradeId)
                ->where('year', $year)
                ->delete();

            foreach ($subjectIds as $subjectId) {
                SkippedGradeSubject::create([
                    'sub_grade_id' => $subGradeId,
                    'subject_id' => $subjectId,
                    'year' => $year,
                    'user_id' => auth()->id(),
                    'note' => $validated['note'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('skipped-grade-subjects.index')
            ->with(
                'success',
                'Skipped subjects updated successfully.'
            );
    }

    public function destroy(
        SkippedGradeSubject $skippedGradeSubject
    ) {
        $skippedGradeSubject->delete();

        return redirect()
            ->route('skipped-grade-subjects.index')
            ->with(
                'success',
                'Skipped subject removed successfully.'
            );
    }
}
