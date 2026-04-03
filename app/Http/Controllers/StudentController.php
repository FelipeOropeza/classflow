<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index()
    {
        return Inertia::render('Students/Index', [
            'students' => Student::with(['guardian', 'enrollments.schoolClass'])->latest()->get(),
            'classes' => SchoolClass::where('academic_year_id', AcademicYear::where('active', true)->first()?->id)->get(),
            'guardians' => User::where('role', 'parent')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'registration_number' => 'required|string|unique:students,registration_number',
            'guardian_id' => 'required|exists:users,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        DB::transaction(function () use ($validated) {
            $student = Student::create([
                'name' => $validated['name'],
                'birth_date' => $validated['birth_date'],
                'registration_number' => $validated['registration_number'],
                'guardian_id' => $validated['guardian_id'],
            ]);

            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $validated['class_id'],
                'academic_year_id' => AcademicYear::where('active', true)->first()->id,
            ]);
        });

        return redirect()->route('students.index');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index');
    }
}
