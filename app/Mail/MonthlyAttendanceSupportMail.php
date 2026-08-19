<?php

namespace App\Mail;

use App\Models\MonthlyAttendanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class MonthlyAttendanceSupportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public MonthlyAttendanceLog $log) {}

    public function build()
    {
        $grade = $this->resolveGrade();

        return $this->subject('Monthly Attendance Report - ' . $this->log->year)
            ->view("emails.monthly-attendance.grade-{$grade}")
            ->with([
                'log' => $this->log,
                'student' => $this->log->student,
                'month' => $this->log->month,
            ]);
    }

    private function resolveGrade(): int
    {
        $gradeName = $this->log->subGrade?->full_name ?? ($this->log->subGrade?->name ?? '');

        if (preg_match('/(?:grade\s*)?([789])/i', $gradeName, $matches)) {
            return (int) $matches[1];
        }

        throw new RuntimeException('Unable to determine Grade 7, 8, or 9.');
    }
}
