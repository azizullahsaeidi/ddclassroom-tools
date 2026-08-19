<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\MonthlyAttendanceLog;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyAttendanceService
{
    public function generate(int $year, int $monthId, int $userId, ?int $subGradeId = null): int
    {
        /*
         * P = Present
         * L = Late -> considered attended
         * A = Absent
         * E = Excused -> excluded from calculation
         */
        $attendanceLogs = AttendanceLog::query()
            ->select('student_id')

            /*
             * A student should normally have one
             * sub-grade during the month.
             */
            ->selectRaw('MAX(sub_grade_id) as sub_grade_id')

            /*
             * Because E is excluded by whereIn(),
             * COUNT(*) = P + L + A
             */
            ->selectRaw('COUNT(*) as total_hours')

            ->selectRaw(
                "
                SUM(
                    CASE
                        WHEN status = 'A'
                        THEN 1
                        ELSE 0
                    END
                ) as total_absences
            ",
            )

            ->where('year', $year)
            ->where('month_id', $monthId)

            /*
             * Excused attendance is not part
             * of the absence calculation.
             */
            ->whereIn('status', ['P', 'L', 'A'])

            ->when($subGradeId, function ($query) use ($subGradeId) {
                $query->where('sub_grade_id', $subGradeId);
            })

            ->groupBy('student_id')
            ->get();

        if ($attendanceLogs->isEmpty()) {
            return 0;
        }

        /*
         * Load support types in one query.
         */
        $students = Student::query()
            ->whereIn('id', $attendanceLogs->pluck('student_id'))
            ->get(['id', 'support_type'])
            ->keyBy('id');

        DB::transaction(function () use ($attendanceLogs, $students, $year, $monthId, $userId) {
            foreach ($attendanceLogs as $attendance) {
                $totalHours = (int) $attendance->total_hours;

                $totalAbsences = (int) $attendance->total_absences;

                /*
                 * Avoid division by zero.
                 */
                $absencePercentage = $totalHours > 0 ? round(($totalAbsences / $totalHours) * 100, 2) : 0;

                $isEligible = $absencePercentage <= 30;

                $student = $students->get($attendance->student_id);

                MonthlyAttendanceLog::query()->updateOrCreate(
                    [
                        'student_id' => $attendance->student_id,
                        'year' => $year,
                        'month_id' => $monthId,
                    ],
                    [
                        'sub_grade_id' => $attendance->sub_grade_id,
                        'total_hours' => $totalHours,
                        'total_absences' => $totalAbsences,
                        'absence_percentage' => $absencePercentage,
                        'support_type' => $student?->support_type,
                        'is_eligible_for_support' => $isEligible,
                        'user_id' => $userId,
                    ],
                );
            }
        });

        return $attendanceLogs->count();
    }
}
