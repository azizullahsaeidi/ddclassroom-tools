<?php

namespace App\Http\Controllers;

use App\Enums\SubjectMinScoreEnum;
use App\Enums\UserTypeEnum;
use App\Http\Requests\Score\CreateMultipleStudentsScoreRequest;
use App\Http\Requests\Score\MultipleStudentRequest;
use App\Http\Requests\ScoreRequest;
use App\Imports\StudentScoreImport;
use App\Models\Score;
use App\Models\SkippedGradeSubject;
use App\Models\Student;
use App\Models\StudentRetakeOpportunity;
use App\Models\SubGrade;
use App\Models\Subject;
use App\Models\User;
use App\Models\Year;
use App\Services\SubjectScore;
use App\Traits\StoreStudentScoreTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ScoreController extends Controller
{
    use StoreStudentScoreTrait;

    public function index(Request $request)
    {
        $query = Score::query()->with(['student:id,name,fa_name,father_name,fa_father_name,uuid', 'subject:id,name', 'subGrade:id,name', 'teacher:id,name']);

        if ($request->search) {
            $name = $request->search;
            $query->where(function ($query) use ($name) {
                $query->whereHas('student', function ($query) use ($name) {
                    $query->whereAny(['name', 'username', 'fa_name', 'fa_father_name', 'father_name', 'email', 'id_number'], 'like', "%$name%");
                });
            });
        }

        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->grade_id) {
            $query->where('sub_grade_id', $request->grade_id);
        }

        $scores = $query->paginate(300);
        $grades = SubGrade::whereIsActive(true)->get();
        $subjects = Subject::all(['id', 'name']);
        $years = Year::all(['id', 'name']);

        return inertia('Score/Index', ['subjects' => $subjects, 'scores' => $scores, 'grades' => $grades, 'years' => $years]);
    }

    public function create()
    {
        $teachers = User::whereUserTypeId(UserTypeEnum::Teacher)->get();
        $grades = SubGrade::whereIsActive(true)->get();
        $subjects = Subject::all(['id', 'name']);
        $years = Year::all(['id', 'name']);
        $types = [['id' => 1, 'name' => 'Midterm Exam'], ['id' => 2, 'name' => 'Final Exam']];

        $midterScores = new SubjectScore(1);
        $finalScores = new SubjectScore(2);

        $midtermSuccessScore = SubjectMinScoreEnum::Middle->value;
        $finalSuccessScore = SubjectMinScoreEnum::Final->value;

        return inertia('Score/Create', [
            'subjects' => $subjects,
            'types' => $types,
            'teachers' => $teachers,
            'grades' => $grades,
            'years' => $years,
            'midtermScores' => $midterScores->getAll(),
            'finalScores' => $finalScores->getAll(),
            'midtermSuccessScore' => $midtermSuccessScore,
            'finalSuccessScore' => $finalSuccessScore,
        ]);
    }

    public function createScores()
    {
        $teachers = User::whereUserTypeId(UserTypeEnum::Teacher)->get();
        $grades = SubGrade::whereIsActive(true)->get();
        $subjects = Subject::all(['id', 'name']);
        $years = Year::all(['id', 'name']);
        $types = [['id' => 1, 'name' => 'Midterm Exam'], ['id' => 2, 'name' => 'Final Exam']];

        $midterScores = new SubjectScore(1);
        $finalScores = new SubjectScore(2);

        $midtermSuccessScore = SubjectMinScoreEnum::Middle->value;
        $finalSuccessScore = SubjectMinScoreEnum::Final->value;

        return inertia('Score/CreateScores', [
            'subjects' => $subjects,
            'types' => $types,
            'teachers' => $teachers,
            'grades' => $grades,
            'years' => $years,
            'midtermScores' => $midterScores->getAll(),
            'finalScores' => $finalScores->getAll(),
            'midtermSuccessScore' => $midtermSuccessScore,
            'finalSuccessScore' => $finalSuccessScore,
        ]);
    }

    public function store(MultipleStudentRequest $request)
    {
        $scores = json_decode($request->studentScores);

        DB::beginTransaction();

        try {
            if (is_array($scores) && ! empty($scores)) {
                $grade = SubGrade::find($request->sub_grade_id)->grade;
                foreach ($scores as $score) {
                    $this->storeScore($request, $score, $grade);
                }

                DB::commit();
            }
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }

        return back();
    }

    public function edit($id)
    {
        $score = Score::with(['student:id,name,father_name', 'subGrade:id,name', 'subject'])->find($id);
        $grades = SubGrade::whereIsActive(true)->get();

        $retakeOpportunity = null;
        $selectedChance = 'first';

        if ($score) {
            $year = Year::where('name', $score->year)->first();
            if ($year) {
                $retakeOpportunity = StudentRetakeOpportunity::where([
                    'student_id' => $score->student_id,
                    'sub_grade_id' => $score->sub_grade_id,
                    'year_id' => $year->id,
                    'subject_id' => $score->subject_id,
                ])->first();

                if ($retakeOpportunity) {
                    if (is_null($retakeOpportunity->third_chance_date)) {
                        $selectedChance = 'second';
                    } else {
                        $selectedChance = 'third';
                    }
                }
            }
        }

        return inertia('Score/Edit', [
            'score' => $score,
            'grades' => $grades,
            'retakeOpportunity' => $retakeOpportunity,
            'selectedChance' => $selectedChance,
        ]);
    }

    public function update(ScoreRequest $request, Score $score)
    {
        DB::beginTransaction();

        try {
            $year = Year::where('name', $score->year)->first();
            $yearId = $year->id;

            // Check current retake opportunity status
            $currentRetakeOpportunity = StudentRetakeOpportunity::where([
                'student_id' => $score->student_id,
                'sub_grade_id' => $score->sub_grade_id,
                'year_id' => $yearId,
                'subject_id' => $score->subject_id,
            ])->first();

            $currentChance = 'first';
            if ($currentRetakeOpportunity) {
                if (is_null($currentRetakeOpportunity->third_chance_date)) {
                    $currentChance = 'second';
                } else {
                    $currentChance = 'third';
                }
            }

            $mainScore = $score->total;

            // Validate chance change
            $requestedChance = $request->chance ?? 'first';

            // If current is third, cannot change to second or first
            if ($currentChance === 'third' && $requestedChance !== 'third') {
                throw new \Exception('Cannot change from third chance to second or first chance.');
            }

            // If current is second, cannot change to first
            if ($currentChance === 'second' && $requestedChance === 'first') {
                throw new \Exception('Cannot change from second chance to first chance.');
            }

            $scoreWhere = [
                'year' => $score->year,
                'sub_grade_id' => $score->sub_grade_id,
                'subject_id' => $score->subject_id,
                'student_id' => $score->student_id,
            ];

            $skippedSubjectsCount = SkippedGradeSubject::where(['sub_grade_id' => $score->sub_grade_id, 'year' => $score->year])->count();

            $studentResultWhere = [
                'year' => $score->year,
                'sub_grade_id' => $score->sub_grade_id,
                'student_id' => $score->student_id,
            ];

            $grade = $score->subGrade->grade;

            $type = $score->type;
            $minAmount = $type == 1 ? SubjectMinScoreEnum::Middle->value : SubjectMinScoreEnum::Final->value;

            $score->update([
                'total' => $request->total,
                'written' => $request->written,
                'activity' => $request->activity,
                'verbal' => $request->verbal,
                'attendance' => $request->attendance,
                'homework' => $request->homework,
                'evaluation' => $request->evaluation,
                'is_passed' => $request->total >= $minAmount ? true : false,
            ]);

            $secondScore = Score::where($scoreWhere)
                ->where('type', $type == 1 ? 2 : 1)
                ->first();

            // Update final score
            Score::where($scoreWhere)
                ->where('type', 3)
                ->update([
                    'written' => (float) $secondScore->written + $score->written,
                    'verbal' => (float) $secondScore->verbal + $score->oral,
                    'attendance' => (float) $secondScore->attendance + $score->attendance,
                    'activity' => (float) $secondScore->activity + $score->activity,
                    'homework' => (float) $secondScore->homework + $score->homework,
                    'evaluation' => (float) $secondScore->evaluation + $score->evaluation,
                    'total' => (float) $secondScore->total + $score->total,
                    'is_passed' => (float) $secondScore->total + $score->total >= SubjectMinScoreEnum::Success->value ? true : false,
                ]);


            $this->updateStudentResult($grade, $studentResultWhere, $type, $skippedSubjectsCount);

            if($grade->id == 9 || $grade->id !=9 && $type == 2){
                if ($requestedChance === 'second') {
                    if ($currentRetakeOpportunity) {
                        $currentRetakeOpportunity->update([
                            'second_chance_score' => $score->total,
                            'second_chance_date' => $currentRetakeOpportunity->second_chance_date ?? now(),
                            'is_passed' => $score->is_passed,
                        ]);
                    } else {
                        StudentRetakeOpportunity::create([
                            'student_id' => $secondScore->student_id,
                            'sub_grade_id' => $secondScore->sub_grade_id,
                            'year_id' => $yearId,
                            'subject_id' => $secondScore->subject_id,
                            'score' => $mainScore,
                            'second_chance_score' => $secondScore->total,
                            'second_chance_date' => now(),
                            'is_passed' => $secondScore->is_passed,
                        ]);
                    }
                } elseif ($requestedChance === 'third' && $currentRetakeOpportunity) {
                    $currentRetakeOpportunity->update([
                        'third_chance_score' => $score->total,
                        'third_chance_date' => $currentRetakeOpportunity->third_chance_date ?? now(),
                        'is_passed' => $score->is_passed,
                    ]);
                }
            }


            DB::commit();
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }

        return redirect('student-score');
    }

    public function createMultipleStudentScores()
    {
        $grades = SubGrade::whereIsActive(true)->get();
        $years = Year::all(['id', 'name']);
        $subjects = Subject::all(['id', 'name']);
        $teachers = User::whereUserTypeId(UserTypeEnum::Teacher)->get();

        return inertia('Score/CreateMultipleStudentsScore', [
            'teachers' => $teachers,
            'subjects' => $subjects,
            'grades' => $grades,
            'years' => $years,
        ]);
    }

    public function storeMultipleStudentsScore(CreateMultipleStudentsScoreRequest $request)
    {
        Excel::import(new StudentScoreImport, $request->file);
    }

    public function deleteScores()
    {
        $grades = SubGrade::whereIsActive(true)->get();
        $subjects = Subject::all(['id', 'name']);
        $years = Year::all(['id', 'name']);
        $types = [['id' => 1, 'name' => 'Midterm Exam'], ['id' => 2, 'name' => 'Final Exam']];

        return inertia('Score/DeleteScores', [
            'subjects' => $subjects,
            'types' => $types,
            'grades' => $grades,
            'years' => $years,
        ]);
    }

    public function deleteStudentScores(Request $request)
    {
        $where = [
            'year' => $request->year,
            'sub_grade_id' => $request->sub_grade_id,
            'subject_id' => $request->subject_id,
        ];

        Score::where($where)
            ->where('type', $request->type_id)
            ->update([
                'written' => null,
                'verbal' => null,
                'attendance' => null,
                'activity' => null,
                'homework' => null,
                'evaluation' => null,
                'total' => null,
                'is_passed' => false,
                'user_id' => auth()->id(),
            ]);

        $score = Score::where($where)
            ->where('type', $request->type_id == 1 ? 2 : 1)->first();

        Score::where($where)
            ->where('type', 3)
            ->update([
                'written' => (float) $score->written,
                'verbal' => (float) $score->oral,
                'attendance' => (float) $score->attendance,
                'activity' => (float) $score->activity,
                'homework' => (float) $score->homework,
                'evaluation' => (float) $score->evaluation,
                'total' => (float) $score->total,
                'is_passed' => (float) $score->total >= SubjectMinScoreEnum::Success->value ? true : false,
            ]);

        $studentResultWhere = [
            'year' => $request->year,
            'sub_grade_id' => $request->sub_grade_id,
            'student_id' => $score->student_id,
        ];
       $skippedSubjectsCount = SkippedGradeSubject::where(['sub_grade_id' => $request->sub_grade_id, 'year' => $request->year])->count();

        $this->updateStudentResult($score->subGrade->grade, $studentResultWhere, 1, $skippedSubjectsCount);
        $this->updateStudentResult($score->subGrade->grade, $studentResultWhere, 2, $skippedSubjectsCount);
    }

    public function createMidtermScoreBasedOnFinal()
    {
        $grades = SubGrade::whereIsActive(true)->get();
        $subjects = Subject::all(['id', 'name']);
        $years = Year::all(['id', 'name']);

        return inertia('Score/CreateMidtermScoreBasedOnFinal', [
            'subjects' => $subjects,
            'grades' => $grades,
            'years' => $years,
        ]);
    }

    public function storeMidtermScoreBasedOnFinal(Request $request)
    {
        DB::beginTransaction();
        try {
            $student = Student::where('id_number', $request->moodle_id)->first();

            if ($student && $request->sub_grade_id == $student->sub_grade_id) {
                $subjects = Subject::where(function ($query) use ($request) {
                    if ($request->subject_id) {
                        $query->where('id', $request->subject_id);
                    }
                })->get();

                foreach ($subjects as $subject) {
                    $where = [
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'year' => $request->year,
                        'sub_grade_id' => $request->sub_grade_id,
                    ];
                    $finalScore = Score::where($where)->where('type', 2)->first();

                    if ($finalScore && $finalScore->total > 0) {
                        $minAmount = SubjectMinScoreEnum::Middle->value;
                        $total = $this->changeScoreFromFinalToMidterm($finalScore->total);
                        Score::where($where) // Update midterm score
                            ->where('type', 1)
                            ->update([
                                'total' => $total,
                                'written' => $this->changeScoreFromFinalToMidterm($finalScore->written),
                                'activity' => $this->changeScoreFromFinalToMidterm($finalScore->activity),
                                'verbal' => $this->changeScoreFromFinalToMidterm($finalScore->verbal),
                                'attendance' => $this->changeScoreFromFinalToMidterm($finalScore->attendance),
                                'homework' => $this->changeScoreFromFinalToMidterm($finalScore->homework),
                                'evaluation' => $this->changeScoreFromFinalToMidterm($finalScore->evaluation),
                                'is_passed' => $total >= $minAmount ? true : false,
                            ]);

                        $midtermScore = Score::where($where)->where('type', 1)->first();

                        Score::where($where)
                            ->where('type', 3)
                            ->update([
                                'written' => (float) $midtermScore->written + $finalScore->written,
                                'verbal' => (float) $midtermScore->oral + $finalScore->oral,
                                'attendance' => (float) $midtermScore->attendance + $finalScore->attendance,
                                'activity' => (float) $midtermScore->activity + $finalScore->activity,
                                'homework' => (float) $midtermScore->homework + $finalScore->homework,
                                'evaluation' => (float) $midtermScore->evaluation + $finalScore->evaluation,
                                'total' => (float) $midtermScore->total + $finalScore->total,
                                'is_passed' => (float) $midtermScore->total + $finalScore->total >= SubjectMinScoreEnum::Success->value ? true : false,
                            ]);
                    }
                }
                $studentResultWhere = [
                    'student_id' => $student->id,
                    'year' => $request->year,
                    'sub_grade_id' => $request->sub_grade_id,
                ];
                $grade = SubGrade::find($request->sub_grade_id)->grade;
                $skippedSubjectsCount = SkippedGradeSubject::where(['sub_grade_id' => $request->sub_grade_id, 'year' => $request->year])->count();
                $this->updateStudentResult($grade, $studentResultWhere, 1, $skippedSubjectsCount);
                $this->updateStudentResult($grade, $studentResultWhere, 2, $skippedSubjectsCount);
                DB::commit();
            } else {
                DB::rollBack();
            }
        } catch (Exception $exception) {
            DB::rollback();

            throw $exception;
        }

        return redirect('add-midterm-score-based-on-final');
    }

    private function changeScoreFromFinalToMidterm($score)
    {
        $finalScore = 60;
        $midtermScore = 40;

        return $score != 0 ? round(($score * $midtermScore) / $finalScore, 2) : 0;
    }
}
