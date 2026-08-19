<?php

namespace App\Http\Controllers;

use App\Jobs\SendMonthlyAttendanceEmail;
use App\Models\Month;
use App\Models\MonthlyAttendanceLog;
use App\Models\SubGrade;
use App\Models\Year;
use App\Services\GenerateMonthlyAttendanceService;
use Illuminate\Http\Request;

class MonthlyAttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $absencePercentage = $request->has('absence_percentage') ? ($request->filled('absence_percentage') ? (float) $request->absence_percentage : null) : 30;

        $query = MonthlyAttendanceLog::query()->with(['student', 'subGrade:id,name,full_name', 'month:id,name', 'user:id,name']);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('month_id')) {
            $query->where('month_id', $request->month_id);
        }

        if ($request->filled('sub_grade_id')) {
            $query->where('sub_grade_id', $request->sub_grade_id);
        }

        if ($request->filled('support_type')) {
            $query->where('support_type', $request->support_type);
        }

        if ($absencePercentage !== null) {
            $query->where('absence_percentage', '>=', $absencePercentage);
        }

        $monthlyAttendanceLogs = $query->orderBy('year', 'desc')->orderBy('month_id', 'desc')->orderByDesc('absence_percentage')->paginate(200)->withQueryString();
        $subGrades = SubGrade::orderBy('full_name')->get(['id', 'name', 'full_name']);
        $months = Month::orderBy('id')->get(['id', 'name']);
        $years = Year::orderBy('name', 'desc')->get(['id', 'name']);

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

    // Generate Monthly Attendance
    public function generate(Request $request, GenerateMonthlyAttendanceService $service)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer'],
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
            ->with('success', $count . ' monthly attendance records generated successfully.');
    }

    // Queue Selected Emails
    public function sendEmails(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:monthly_attendance_logs,id'],
        ]);


        $logs = MonthlyAttendanceLog::query()->whereIn('id', $validated['ids'])->where('is_eligible_for_support', false)->where('is_sent', false)->get();

        if ($logs->isEmpty()) {
            return redirect()->back()->with('error', 'No eligible records were selected for email sending.');
        }

        foreach ($logs as $log) {
            SendMonthlyAttendanceEmail::dispatch($log->id);
        }

        return redirect()->back()->with('success', $logs->count() . ' email(s) added to the queue.');
    }
}
