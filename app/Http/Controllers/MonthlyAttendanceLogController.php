<?php

namespace App\Http\Controllers;

use App\Exports\ExportMonthlyTopUpReport;
use App\Jobs\SendMonthlyAttendanceEmail;
use App\Models\Month;
use App\Models\MonthlyAttendanceLog;
use App\Models\SubGrade;
use App\Models\Year;
use App\Services\GenerateMonthlyAttendanceService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyAttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $absencePercentage = $request->has('absence_percentage')
            ? ($request->filled('absence_percentage') ? $request->absence_percentage : null)
            : 30;

        $query = MonthlyAttendanceLog::query()
            ->select([
                'id',
                'student_id',
                'sub_grade_id',
                'total_hours',
                'total_absences',
                'is_eligible_for_support',
                'is_sent',
            ])
            ->with([
                'student:id,name,father_name,phone',
                'subGrade:id,full_name',
            ]);

        if ($request->year) {
            $query->where('year', $request->year);
        }

        if ($request->month_id) {
            $query->where('month_id', $request->month_id);
        }

        if ($request->sub_grade_id) {
            $query->where('sub_grade_id', $request->sub_grade_id);
        }

        if ($request->support_type) {
            $query->where('support_type', $request->support_type);
        }

        if ($absencePercentage !== null) {
            $query->where('absence_percentage', '>=', $absencePercentage);
        }

        $monthlyAttendanceLogs = $query
            ->orderByDesc('year')
            ->orderByDesc('month_id')
            ->orderByDesc('absence_percentage')
            ->paginate(2000)
            ->appends($request->query());

        $subGrades = SubGrade::whereIsActive(true)->orderBy('full_name')->get(['id', 'name', 'full_name']);
        $months = Month::all(['id', 'name']);
        $years = Year::all(['id', 'name']);

        return inertia('MonthlyAttendanceLog/Index', [
            'monthlyAttendanceLogs' => $monthlyAttendanceLogs,
            'subGrades' => $subGrades,
            'months' => $months,
            'years' => $years,
            'filters' => [
                'year' => $request->year,
                'month_id' => $request->month_id,
                'sub_grade_id' => $request->sub_grade_id,
                'absence_percentage' => $absencePercentage,
                'support_type' => $request->support_type,
            ],
        ]);
    }

    public function export(Request $request)
    {
        $monthName = $request->month_id
            ? Month::where('id', $request->month_id)->value('name')
            : 'All Months';

        $filters = [
            'year' => $request->year,
            'month_id' => $request->month_id,
            'month_name' => $monthName,
            'sub_grade_id' => $request->sub_grade_id,
            'absence_percentage' => $request->has('absence_percentage')
                ? ($request->filled('absence_percentage') ? $request->absence_percentage : null)
                : 30,
            'support_type' => $request->support_type,
        ];
        $year = $request->year ?: 'all-years';
        $month = $request->month_id ?: 'all-months';

        return Excel::download(
            new ExportMonthlyTopUpReport($filters),
            "monthly-top-up-report-{$year}-{$month}.xlsx",
        );
    }

    public function create(Request $request, GenerateMonthlyAttendanceService $service)
    {
        $results = collect();

        if ($request->year && $request->month_id) {
            $results = $service->getResults(
                (int) $request->year,
                (int) $request->month_id,
                $request->sub_grade_id ? (int) $request->sub_grade_id : null,
            );
        }

        $subGrades = SubGrade::whereIsActive(true)->orderBy('full_name')->get(['id', 'name', 'full_name']);
        $months = Month::all(['id', 'name']);
        $years = Year::all(['id', 'name']);

        return inertia('MonthlyAttendanceLog/Create', [
            'results' => $results,
            'subGrades' => $subGrades,
            'months' => $months,
            'years' => $years,
            'filters' => [
                'year' => $request->year,
                'month_id' => $request->month_id,
                'sub_grade_id' => $request->sub_grade_id,
            ],
            'hasPreview' => (bool) ($request->year && $request->month_id),
        ]);
    }

    public function generate(Request $request, GenerateMonthlyAttendanceService $service)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'exists:years,name'],
            'month_id' => ['required', 'integer', 'exists:months,id'],
            'sub_grade_id' => ['nullable', 'integer', 'exists:sub_grades,id'],
        ]);

        $count = $service->generate(
            year: (int) $validated['year'],
            monthId: (int) $validated['month_id'],
            userId: auth()->id(),
            subGradeId: isset($validated['sub_grade_id']) ? (int) $validated['sub_grade_id'] : null,
        );

        return redirect()
            ->route('monthly-attendance-logs.index', [
                'year' => $validated['year'],
                'month_id' => $validated['month_id'],
                'sub_grade_id' => $validated['sub_grade_id'] ?? null,
                'absence_percentage' => 30,
            ])
            ->with('success', $count.' monthly attendance records generated successfully.');
    }

    // Queue Selected Emails
    public function sendEmails(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:monthly_attendance_logs,id'],
        ]);
        $logIds = MonthlyAttendanceLog::query()
            ->whereIn('id', $validated['ids'])
            ->where('is_eligible_for_support', false)
            ->where('is_sent', false)
            ->pluck('id');

        if ($logIds->isEmpty()) {
            return redirect()->back()->with('error', 'No unsent, ineligible records were selected.');
        }

        foreach ($logIds as $logId) {
            SendMonthlyAttendanceEmail::dispatch($logId);
        }

        return redirect()->back()->with('success', $logIds->count().' email(s) added to the queue.');
    }
}
