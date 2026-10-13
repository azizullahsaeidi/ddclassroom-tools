<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ClassResponsibleController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HandBookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MonthlyAttendanceLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\ShoqaController;
use App\Http\Controllers\SkippedGradeSubjectController;
use App\Http\Controllers\StudentClassPromotionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentResultCardController;
use App\Http\Controllers\StudentResultController;
use App\Http\Controllers\StudentRetakeOpportunityController;
use App\Http\Controllers\SubGradeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TwoFactorAuthSetupController;
use App\Http\Controllers\TwoFactorChallengeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\YearController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified', '2fa'])->group(function () {
    $permissionResource = function (string $name, string $controller, string $section): void {
        Route::resource($name, $controller)->only(['index', 'show'])->middleware("permission:$section.view");
        Route::resource($name, $controller)->only(['create', 'store'])->middleware("permission:$section.create");
        Route::resource($name, $controller)->only(['edit', 'update'])->middleware("permission:$section.edit");
        Route::resource($name, $controller)->only('destroy')->middleware("permission:$section.delete");
    };

    Route::post('general-attendance-score', [AttendanceLogController::class, 'generalAttendanceScore'])->middleware('permission:attendance.edit')->name('general-attendance-score');
    Route::get('create-student-shoqa-score', [ShoqaController::class, 'createStudentShoqaScore'])->middleware('permission:attendance.create');
    Route::get('clear-all-attendance-log', [AttendanceLogController::class, 'clearAllAttendanceLog'])->middleware('permission:attendance.delete');
    Route::get('/', [HomeController::class, 'index'])->middleware('permission:dashboard.view');
    Route::get('/dashboard', [HomeController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');
    Route::resource('users', UserController::class)->only('index')->middleware('permission:users.view');
    Route::resource('users', UserController::class)->only(['create', 'store'])->middleware('permission:users.create');
    Route::resource('users', UserController::class)->only(['edit', 'update'])->middleware('permission:users.edit');
    Route::resource('roles', RoleController::class)->only('index')->middleware('permission:roles.view');
    Route::resource('roles', RoleController::class)->only(['create', 'store'])->middleware('permission:roles.create');
    Route::resource('roles', RoleController::class)->only(['edit', 'update'])->middleware('permission:roles.edit');
    Route::resource('roles', RoleController::class)->only('destroy')->middleware('permission:roles.delete');
    Route::resource('permissions', PermissionController::class)->only('index')->middleware('permission:permissions.view');
    Route::resource('permissions', PermissionController::class)->only(['create', 'store'])->middleware('permission:permissions.create');
    Route::resource('permissions', PermissionController::class)->only(['edit', 'update'])->middleware('permission:permissions.edit');
    Route::resource('permissions', PermissionController::class)->only('destroy')->middleware('permission:permissions.delete');

    // Report routes
    Route::middleware('permission:reports.view')->controller(ReportController::class)->group(function () {
        Route::get('reports/student-results', 'studentResults')->name('reports.student-results');
        Route::get('reports/subject-scores', 'subjectScores')->name('reports.subject-scores');
        Route::get('reports/subject-statistics', 'subjectStatistics')->name('reports.subject-statistics');
        Route::get('reports/grade-9', 'grade9Report')->name('reports.grade-9');
        Route::get('reports/all-students-subject-scores', 'allStudentsSubjectScores')->name('reports.all-students-subject-scores');

    });
    Route::middleware('permission:reports.export')->controller(ReportController::class)->group(function () {
        Route::get('reports/student-results/export', 'exportStudentResults')->name('reports.student-results.export');
        Route::get('reports/subject-scores/export', 'exportSubjectScores')->name('reports.subject-scores.export');
        Route::get('reports/subject-statistics/export', 'exportSubjectStatistics')->name('reports.subject-statistics.export');
        Route::get('reports/grade-9/export', 'exportGrade9')->name('reports.grade-9.export');
        Route::get('reports/all-students-subject-scores/export', 'exportAllStudentsSubjectScores')->name('reports.all-students-subject-scores.export');
    });

    Route::get('students/create/multiple', [StudentController::class, 'createMultipleStudents'])->middleware('permission:students.create');
    Route::get('students/retake-opportunities', [StudentRetakeOpportunityController::class, 'index'])->middleware('permission:students.view')->name('students.retake-opportunities');
    Route::get('students/retake-opportunities/export', [StudentRetakeOpportunityController::class, 'export'])->middleware('permission:students.view')->name('students.retake-opportunities.export');
    $permissionResource('students', StudentController::class, 'students');
    Route::get('edit-student-info', [StudentController::class, 'editStudentInfo'])->middleware('permission:students.edit');

    Route::post('update-student-info', [StudentController::class, 'updateStudentInfo'])->middleware('permission:students.edit')->name('students.update-student-info');

    Route::post('storeMultipleStudents', [StudentController::class, 'storeMultipleStudents'])->middleware('permission:students.create')->name('students.store-multiple-student');
    Route::post('student-uploading-sample-file', [StudentController::class, 'studentUpladingSampleFile'])->middleware('permission:students.create')->name('student-uploading-sample-file');
    Route::get('email-handbook/{uuid}', [StudentController::class, 'emailHandbook'])->middleware('permission:students.edit');
    Route::get('handbooks', [HandBookController::class, 'index'])->middleware('permission:students.view')->name('handbooks.index');
    Route::post('handbooks.send-handbook', [HandBookController::class, 'sendHandbook'])->middleware('permission:students.edit')->name('handbooks.send-handbook');

    Route::controller(StudentResultController::class)->group(function () {
        Route::get('student-result', 'index')->middleware('permission:students.view')->name('student-result.index');
        Route::post('student-result.send-score', 'sendScore')->middleware('permission:students.create')->name('student-result.send-score');
        Route::get('student-result/create/multiple', 'createMultipleStudentScores')->middleware('permission:students.create');
        Route::post('storeMultipleStudentsScore', 'storeMultipleStudentsScore')->middleware('permission:students.create')->name('student-result.store-multiple-student-score');
    });

    Route::controller(AttendanceController::class)->group(function () {
        Route::get('student-attendance', 'index')->middleware('permission:attendance.view')->name('student-attendance.index');
        Route::post('student-attendance.send-score', 'sendScore')->middleware('permission:attendance.create')->name('student-attendance.send-attendance');
        Route::get('student-attendance/create/multiple', 'createMultipleStudentAttendance')->middleware('permission:attendance.create');
        Route::post('store-multiple-students-attendance', 'storeMultipleStudentsAttendance')->middleware('permission:attendance.create')->name('student-attendance.store-multiple-student-attendance');
    });

    Route::controller(AttendanceLogController::class)->group(function () {
        Route::get('student-attendance-log', 'index')->middleware('permission:attendance.view')->name('student-attendance-log.index');
        Route::post('student-attendance-log.send-score', 'sendScore')->middleware('permission:attendance.create')->name('student-attendance-log.send-attendance');
        Route::get('student-attendance-log/create/multiple', 'createMultipleStudentAttendance')->middleware('permission:attendance.create');
        Route::post('store-multiple-students-attendance-log', 'storeMultipleStudentsAttendanceLog')->middleware('permission:attendance.create')->name('student-attendance-log.store-multiple-student-attendance');
    });
    Route::middleware('permission:reports.view')->controller(AttendanceLogController::class)->group(function () {
        Route::get('reports/attendance-log', 'studentAttendanceLogReports')->name('reports.attendance-log');
    });
    Route::get('get-attendance-log-report-as-excel', [AttendanceLogController::class, 'getAttendanceLogReportAsExcel'])->middleware('permission:reports.export');

    $permissionResource('subjects', SubjectController::class, 'subjects');
    $permissionResource('countries', CountryController::class, 'countries');
    $permissionResource('years', YearController::class, 'years');
    Route::post('toggle-country-is-active', [CountryController::class, 'toggleIsActive'])->middleware('permission:countries.edit');
    $permissionResource('results', ResultController::class, 'results');
    Route::post('toggle-result-is-active', [ResultController::class, 'toggleIsActive'])->middleware('permission:results.edit');
    $permissionResource('sub-grades', SubGradeController::class, 'grades');
    $permissionResource('sub-grade-subject-semesters', \App\Http\Controllers\SubGradeSubjectSemesterController::class, 'semester-subjects');
    $permissionResource('class-responsible', ClassResponsibleController::class, 'homeroom-teachers');
    Route::post('toggle-sub-grade-is-active', [SubGradeController::class, 'toggleIsActive'])->middleware('permission:grades.edit');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('logs', [LogViewerController::class, 'index'])->middleware('permission:logs.view');

    Route::middleware('permission:students.view')->controller(StudentResultCardController::class)->group(function () {
        Route::get('student-hand-book/{uuid}', 'studentHandBook');
        Route::get('student-result-card/{uuid}/{year}/{studentResult}', 'studentResultCard');
    });

    Route::controller(ScoreController::class)->group(function () {
        Route::get('student-score', 'index')->middleware('permission:scores.view')->name('student-score.index');
        Route::get('student-score/create', 'create')->middleware('permission:scores.create')->name('student-score.create');
        Route::get('student-score/create-scores', 'createScores')->middleware('permission:scores.create')->name('student-score.create-scores');
        Route::get('student-score/delete-scores', 'deleteScores')->middleware('permission:scores.delete')->name('student-score.delete-scores');
        Route::get('student-score/{score}/edit', 'edit')->middleware('permission:scores.edit');
        Route::post('student-score', 'store')->middleware('permission:scores.create')->name('student-score.store');
        Route::post('delete-scores', 'deleteStudentScores')->middleware('permission:scores.delete')->name('delete-scores');
        Route::put('student-score/{score}', 'update')->middleware('permission:scores.edit')->name('student-score.update');
        Route::get('add-midterm-score-based-on-final', 'createMidtermScoreBasedOnFinal')->middleware('permission:scores.create');
        Route::post('add-midterm-score-based-on-final', 'storeMidtermScoreBasedOnFinal')->middleware('permission:scores.create')->name('add-midterm-score-based-on-final');
    });

    Route::post('get-students', [EnrollmentController::class, 'getStudents'])->middleware('permission:students.view|attendance.view|scores.view')->name('get-students');

    Route::controller(ShoqaController::class)->group(function () {
        Route::get('shoqa', 'index')->middleware('permission:shoqa.view');
        Route::get('get-shoqa-as-excel', 'getShoqaAsExcel')->middleware('permission:shoqa.export');
    });

    Route::get('get-student-result-as-excel', [StudentResultController::class, 'getStudentResultAsExcel'])->middleware('permission:reports.export|students.view');

    Route::controller(StudentClassPromotionController::class)->group(function () {
        Route::get('student-class-promotion', 'index')->middleware('permission:students.view');
        Route::post('student-class-promotion', 'store')->middleware('permission:students.edit')->name('student-class-promotion');
    });

    $permissionResource('skipped-grade-subjects', SkippedGradeSubjectController::class, 'skipped-subjects');

    // Backup routes are protected by the backups section permission.
    Route::controller(BackupController::class)->group(function () {
        Route::get('backup', 'index')->middleware('permission:backups.view')->name('backup.index');
        Route::get('backup/download/{disk}/{path}', 'download')->middleware('permission:backups.download')->name('backup.download')->where('path', '.*');
    });

    Route::prefix('monthly-attendance-logs')->controller(MonthlyAttendanceLogController::class)->group(function () {
        Route::get('/', 'index')->middleware('permission:attendance.view')->name('monthly-attendance-logs.index');
        Route::get('/export', 'export')->middleware('permission:attendance.view')->name('monthly-attendance-logs.export');
        Route::get('/generate', 'create')->middleware('permission:attendance.create')->name('monthly-attendance-logs.create');
        Route::post('/generate', 'generate')->middleware('permission:attendance.create')->name('monthly-attendance-logs.generate');
        Route::post('/send-emails', 'sendEmails')->middleware('permission:attendance.edit')->name('monthly-attendance-logs.send-emails');
    });
});

Route::middleware(['auth'])
    ->controller(TwoFactorAuthSetupController::class)
    ->group(function () {
        Route::get('2fa/index', 'index');
        Route::get('2fa/enable', 'store')->name('enable-2fa');
        Route::post('2fa/disable', 'destroy')->name('disable-2fa');
    });
Route::get('two-factor-challenge-backup-code', [TwoFactorChallengeController::class, 'index']);
Route::get('result-card/{uuid}/{year}/{studentResult}', [StudentResultCardController::class, 'resultCard']);
Route::get('certificate/{uuid}/{year}/{studentResult}/{semester?}', [StudentResultCardController::class, 'grade9Certificate']);
