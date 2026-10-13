<?php

namespace App\Exports;

ini_set('max_execution_time', 120);

use App\Models\Month;
use App\Models\Student;
use App\Traits\AttendanceLogConditionTrait;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportAttendanceLogReport implements FromView, ShouldAutoSize
{
    use AttendanceLogConditionTrait;

    public function __construct(private array $data) {}

    public function view(): View
    {

        $request = (object) $this->data;

        $query = Student::query()->select(['id', 'name', 'father_name', 'fa_name', 'fa_father_name', 'province', 'email', 'phone', 'id_number', 'sub_grade_id'])
            ->with(['subGrade:id,name,full_name'])->whereHas('enrollments', function($query) use ($request){
                $query->where('year', $request->year);
            });
        $query
            ->withCount([
                'attendanceLogs' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'present' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'absent' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'late' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ])
            ->withCount([
                'excused' => function ($query) use ($request) {
                    return $this->filterAttendance($query, $request);
                },
            ]);

        // if ($request->search) {
        //     $name = $request->search;
        //     $query->where(function ($query) use ($name) {
        //         $query->whereAny(['name', 'last_name', 'username', 'father_name', 'fa_name', 'fa_last_name', 'fa_father_name', 'email', 'phone', 'id_number'], 'like', "%$name%");
        //     });
        // }
        if ($request->sub_grade_id) {
            $query->where('sub_grade_id', $request->sub_grade_id);
        }

        if ($request->country_id) {
            $query->where('country_id', $request->country_id);
        }



        $students = $query->orderBy('absent_count')->get();

        return view('attendance.export-attendance-log-report-as-excel', [
            'students' => $students,
            'year' => $request->year,
            'month' => Month::find($request->month_id),
        ]);
    }
}
