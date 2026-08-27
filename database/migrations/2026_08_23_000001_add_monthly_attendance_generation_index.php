<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->index(
                ['year', 'month_id', 'status', 'sub_grade_id', 'student_id'],
                'attendance_logs_monthly_summary_index',
            );
        });

        Schema::table('monthly_attendance_logs', function (Blueprint $table) {
            $table->index(
                ['year', 'month_id', 'absence_percentage'],
                'monthly_attendance_period_absence_index',
            );
            $table->index(
                ['sub_grade_id', 'support_type'],
                'monthly_attendance_grade_support_index',
            );
        });
    }

    public function down(): void
    {
        Schema::table('monthly_attendance_logs', function (Blueprint $table) {
            $table->dropIndex('monthly_attendance_period_absence_index');
            $table->dropIndex('monthly_attendance_grade_support_index');
        });

        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropIndex('attendance_logs_monthly_summary_index');
        });
    }
};
