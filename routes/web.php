<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherLinkController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\SchoolEventController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EnrollmentController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Enrollments & Progression (Board of Class)
    Route::get('/enrollments/review', [EnrollmentController::class, 'review'])->name('enrollments.review');
    Route::patch('/enrollments/{enrollment}/status', [EnrollmentController::class, 'updateStatus'])->name('enrollments.status.update');
    
    // User Governance
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    
    // Academic Periods
    Route::get('/terms', [TermController::class, 'index'])->name('terms.index');
    Route::patch('/terms/{term}', [TermController::class, 'update'])->name('terms.update');
    
    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
    
    // Classes
    Route::get('/classes', [SchoolClassController::class, 'index'])->name('classes.index');
    Route::post('/classes', [SchoolClassController::class, 'store'])->name('classes.store');
    
    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::post('/students/{student}/enroll', [StudentController::class, 'enroll'])->name('students.enroll');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Teachers
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

    // Academic Links (Class-Subject-Teacher)
    Route::get('/academic-links', [TeacherLinkController::class, 'index'])->name('academic-links.index');
    Route::post('/academic-links', [TeacherLinkController::class, 'store'])->name('academic-links.store');
    Route::delete('/academic-links/{link}', [TeacherLinkController::class, 'destroy'])->name('academic-links.destroy');

    // Attendance (Class Diary)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    // Grades (Academic Performance)
    Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
    Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');

    // Assessments Management
    Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');

    // School Calendar
    Route::resource('school-events', SchoolEventController::class);
    
    // Guardian Portal
    Route::get('/guardian/report-card', [GuardianController::class, 'reportCard'])->name('guardian.report-card');
    Route::get('/guardian/report-card/pdf', [GuardianController::class, 'exportPdf'])->name('guardian.report-card.pdf');
    Route::get('/guardian/attendance', [GuardianController::class, 'attendance'])->name('guardian.attendance');
    Route::get('/guardian/student/create', [GuardianController::class, 'createStudent'])->name('guardian.student.create');
    Route::post('/guardian/student', [GuardianController::class, 'storeStudent'])->name('guardian.student.store');
});
