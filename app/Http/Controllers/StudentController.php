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
        $activeYear = AcademicYear::where('active', true)->first();
        
        return Inertia::render('Students/Index', [
            'students' => Student::with(['guardian', 'enrollments.schoolClass'])->latest()->get(),
            'classes' => SchoolClass::where('academic_year_id', $activeYear?->id)->get(),
            'guardians' => User::where('role', 'guardian')->get()
        ]);
    }

    public function enroll(Request $request, Student $student)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $activeYear = AcademicYear::where('active', true)->first();

        // Atualizar matrícula existente para ATIVA e vincular turma
        Enrollment::updateOrCreate(
            ['student_id' => $student->id, 'academic_year_id' => $activeYear->id],
            ['class_id' => $validated['class_id'], 'status' => 'active']
        );

        return back()->with('success', 'Aluno enturmado com sucesso!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'registration_number' => 'required|string|unique:students,registration_number',
            'guardian_id' => 'required|exists:users,id',
        ]);

        $student = Student::create([
            'name' => $validated['name'],
            'birth_date' => $validated['birth_date'],
            'registration_number' => $validated['registration_number'],
            'guardian_id' => $validated['guardian_id'],
        ]);

        return redirect()->route('students.index')->with('success', 'Aluno registrado, agora realize a enturmação.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index');
    }
}
