<?php

namespace App\Http\Controllers;

use App\Models\ClassResponsible;
use App\Models\GradeSubject;
use App\Models\Result;
use App\Models\SkippedGradeSubject;
use App\Models\Student;
use App\Models\StudentResult;
use App\Models\SubGradeSubjectSemester;
use Illuminate\Http\Request;

class StudentResultCardController extends Controller
{
    public function studentHandBook($uuid)
    {
        $student = Student::where('uuid', $uuid)->firstOrFail();

        return view('students.show', compact('student'));
    }

    public function studentResultCard(Request $request, $uuid, $year, $studentResultId)
    {

        $studentResult = StudentResult::find($studentResultId);
        $subGradeId = $studentResult->sub_grade_id;
        $student = Student::where('uuid', $uuid)
            ->with('middleAttendance', function ($query) use ($year, $subGradeId) {
                $query->where([
                    'year' => $year,
                    'sub_grade_id' => $subGradeId,
                ]);
            })
            ->with('finalAttendance', function ($query) use ($year, $subGradeId) {
                $query->where([
                    'year' => $year,
                    'sub_grade_id' => $subGradeId,
                ]);
            })
            ->firstOrFail();

        $responsible = ClassResponsible::with('teacher:id,name,signature,en_name')
            ->where([
                'year' => $year,
                'sub_grade_id' => $student->sub_grade_id,
            ])
            ->first();

        $studentId = $student->id;
        $where = [
            'year' => $year,
            'student_id' => $studentId,
            'sub_grade_id' => $subGradeId,
        ];

        $skippedSubjects = SkippedGradeSubject::where(['year' => $year, 'sub_grade_id' => $subGradeId])->pluck('subject_id');

        $subjects = GradeSubject::with(['grade:id,name', 'subject'])
            ->with('subject.middle', function ($query) use ($where) {
                $query->where($where);
            })
            ->with('subject.final', function ($query) use ($where) {
                $query->where($where);
            })
            ->with('subject.finalResult', function ($query) use ($where) {
                $query->where($where);
            })
            ->where('grade_id', $student->subGrade->grade_id)
            ->where(function($query) use ($skippedSubjects) {
                if($skippedSubjects){
                    $query->whereNotIn('subject_id', $skippedSubjects);
                }
            })
            ->get();

        $results = Result::all();

        $year = base64_encode($year);

        $semester = $request->semester ? $request->semester: 1;

        $studentResultId = base64_encode($studentResultId);

        $qrCode = url("result-card/$uuid/$year/$studentResultId");

        if ($request->en_result && $request->en_result == 'english') {
            return view('students.student-en-result-card', compact('student', 'subjects', 'results', 'responsible', 'studentResult', 'year', 'qrCode'));
        } elseif ($request->en_result && $request->en_result == 'grade-9') {
            // Check if full report is requested (both semesters)
            if ($request->full_report == '1' || $request->full_report == 'true') {

                $qrCode = url("certificate/$uuid/$year/$studentResultId/2?final_report=1&en_result=english");
                return view('students.grade-9-report-card', compact('student', 'subjects', 'results', 'responsible', 'year', 'studentResult', 'qrCode'));
            }
            // Otherwise show single semester view
            $semesterEncode = base64_encode($semester);
            $qrCode = url("certificate/$uuid/$year/$studentResultId/$semesterEncode");
            return view('students.grade-9-result-card', compact('student', 'subjects', 'results', 'responsible', 'semester', 'year', 'studentResult', 'qrCode'));
        }

        return view('students.student-result-card', compact('student', 'subjects', 'results', 'responsible', 'year', 'studentResult', 'qrCode'));
    }

    public function resultCard(Request $request, $uuid, $year, $studentResultId)
    {
        $year = base64_decode($year);
        $studentResultId = base64_decode($studentResultId);
        $studentResult = StudentResult::findOrFail($studentResultId);

        $subGradeId = $studentResult->sub_grade_id;
        $student = Student::where('uuid', $uuid)
            ->with('middleAttendance', function ($query) use ($year, $subGradeId) {
                $query->where([
                    'year' => $year,
                    'sub_grade_id' => $subGradeId,
                ]);
            })
            ->with('finalAttendance', function ($query) use ($year, $subGradeId) {
                $query->where([
                    'year' => $year,
                    'sub_grade_id' => $subGradeId,
                ]);
            })
            ->firstOrFail();

        $responsible = ClassResponsible::with('teacher:id,name,signature,en_name')
            ->where([
                'year' => $year,
                'sub_grade_id' => $student->sub_grade_id,
            ])
            ->first();

        $studentId = $student->id;
        $where = [
            'year' => $year,
            'student_id' => $studentId,
            'sub_grade_id' => $subGradeId,
        ];

        $skippedSubjects = SkippedGradeSubject::where(['year' => $year, 'sub_grade_id' => $subGradeId])->pluck('subject_id');

        $subjects = GradeSubject::with(['grade:id,name', 'subject'])
            ->with('subject.middle', function ($query) use ($where) {
                $query->where($where);
            })
            ->with('subject.final', function ($query) use ($where) {
                $query->where($where);
            })
            ->with('subject.finalResult', function ($query) use ($where) {
                $query->where($where);
            })
            ->where('grade_id', $student->subGrade->grade_id)
            ->where(function($query) use ($skippedSubjects) {
                if($skippedSubjects){
                    $query->whereNotIn('subject_id', $skippedSubjects);
                }
            })
            ->get();
        $results = Result::all();

        $year = base64_encode($year);

        $studentResultId = base64_encode($studentResultId);

        $qrCode = url("result-card/$uuid/$year/$studentResultId");

        return view('students.result-card', compact('student', 'subjects', 'results', 'responsible', 'year', 'studentResult', 'qrCode'));
    }

    public function grade9Certificate(Request $request, $uuid, $year, $studentResultId, $semester)
    {
        $year = base64_decode($year);
        $studentResultId = base64_decode($studentResultId);
        $studentResult = StudentResult::findOrFail($studentResultId);

        $subGradeId = $studentResult->sub_grade_id;
        $student = Student::where('uuid', $uuid)
            ->with('middleAttendance', function ($query) use ($year, $subGradeId) {
                $query->where([
                    'year' => $year,
                    'sub_grade_id' => $subGradeId,
                ]);
            })
            ->with('finalAttendance', function ($query) use ($year, $subGradeId) {
                $query->where([
                    'year' => $year,
                    'sub_grade_id' => $subGradeId,
                ]);
            })
            ->firstOrFail();

        $responsible = ClassResponsible::with('teacher:id,name,signature,en_name')
            ->where([
                'year' => $year,
                'sub_grade_id' => $student->sub_grade_id,
            ])
            ->first();

        $studentId = $student->id;
        $where = [
            'year' => $year,
            'student_id' => $studentId,
            'sub_grade_id' => $subGradeId,
        ];

        // Get semester from request, default to 1 (first semester)
        $semester = base64_decode($semester);

        // Get sub_grade-specific semester assignments for this year
        $subGradeSemesterSubjects = SubGradeSubjectSemester::where('sub_grade_id', $subGradeId)
            ->where('semester', $semester)
            ->where('year', $year)
            ->with(['subject' => function ($query) use ($where) {
                $query->with(['middle' => function ($q) use ($where) {
                    $q->where($where);
                }])
                ->with(['final' => function ($q) use ($where) {
                    $q->where($where);
                }])
                ->with(['finalResult' => function ($q) use ($where) {
                    $q->where($where);
                }]);
            }])
            ->get();

        // Map to match the expected structure for the view
        $subjects = GradeSubject::with(['grade:id,name', 'subject'])
           ->with('subject.middle', function ($query) use ($where) {
                $query->where($where);
            })
            ->with('subject.final', function ($query) use ($where) {
                $query->where($where);
            })
            ->with('subject.finalResult', function ($query) use ($where) {
               $query->where($where);
            })
            ->where('grade_id', $student->subGrade->grade_id)
            ->get();


        $results = Result::all();

        $year = base64_encode($year);

        $studentResultId = base64_encode($studentResultId);

        $qrCode = url("certificate/$uuid/$year/$studentResultId/".base64_encode($semester));

        if($request->final_report){
            if($request->final_report == 1){

                $qrCode = url("certificate/$uuid/$year/$studentResultId/".base64_encode($semester).'?en_result=english&final_report=1');

                return view('students.public-grade-9-result-card', compact('student', 'subjects', 'results', 'responsible', 'year', 'studentResult', 'qrCode', 'semester'));
            }
            return abort(404, 'Page not found');
        }

        return view('students.grade-9-certificate', compact('student', 'subjects', 'results', 'responsible', 'year', 'studentResult', 'qrCode', 'semester'));
    }
}
