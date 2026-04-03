<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('active', true)->first();

        return Inertia::render('Dashboard', [
            'statsData' => [
                'totalStudents' => Student::count(),
                'activeClasses' => $activeYear ? SchoolClass::where('academic_year_id', $activeYear->id)->count() : 0,
                'attendanceRate' => 94, // Dash mock
                'newEnrollments' => Enrollment::whereMonth('created_at', now()->month)->count(),
            ],
            'activeYear' => $activeYear?->name ?? 'Nenhum'
        ]);
    }
}
