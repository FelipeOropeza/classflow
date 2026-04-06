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
            'students'  => Student::with(['guardian', 'enrollments.schoolClass'])->latest()->get(),
            'classes'   => SchoolClass::where('academic_year_id', $activeYear?->id)->get(),
            'guardians' => User::where('role', 'guardian')->get()
        ]);
    }

    public function enroll(Request $request, Student $student)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
        ], [
            'class_id.required' => 'Selecione uma turma para a enturmação.',
            'class_id.exists'   => 'A turma selecionada não é válida.',
        ]);

        $activeYear = AcademicYear::where('active', true)->first();

        if (!$activeYear) {
            return back()->withErrors(['geral' => 'Nenhum ano letivo ativo encontrado.']);
        }

        Enrollment::updateOrCreate(
            ['student_id' => $student->id, 'academic_year_id' => $activeYear->id],
            ['class_id' => $validated['class_id'], 'status' => 'active']
        );

        return back()->with('success', 'Aluno enturmado com sucesso!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|min:3|max:255',
            'birth_date'          => 'required|date|before:today',
            'registration_number' => 'required|string|max:50|unique:students,registration_number',
            'guardian_id'         => 'required|exists:users,id',
        ], [
            'name.required'                => 'O nome do aluno é obrigatório.',
            'name.min'                     => 'O nome deve ter pelo menos 3 caracteres.',
            'birth_date.required'          => 'A data de nascimento é obrigatória.',
            'birth_date.date'              => 'Informe uma data de nascimento válida.',
            'birth_date.before'            => 'A data de nascimento não pode ser hoje ou no futuro.',
            'registration_number.required' => 'O número de matrícula é obrigatório.',
            'registration_number.unique'   => 'Este número de matrícula já está em uso.',
            'guardian_id.required'         => 'Selecione o responsável pelo aluno.',
            'guardian_id.exists'           => 'O responsável selecionado não é válido.',
        ]);

        Student::create([
            'name'                => $validated['name'],
            'birth_date'          => $validated['birth_date'],
            'registration_number' => $validated['registration_number'],
            'guardian_id'         => $validated['guardian_id'],
        ]);

        return redirect()->route('students.index')->with('success', 'Aluno registrado! Realize a enturmação na listagem.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Aluno removido com sucesso.');
    }
}
