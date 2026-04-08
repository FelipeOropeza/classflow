<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\ClassSubject;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TeacherScheduleController extends Controller
{
    /**
     * Exibe o quadro de horários pessoal do professor logado.
     */
    public function index(Request $request)
    {
        $teacher = $request->user();

        // Todos os class_subjects onde este professor está vinculado
        $classSubjectIds = ClassSubject::where('teacher_id', $teacher->id)->pluck('id');

        // Todos os horários do professor, agrupados por turma
        $schedules = Schedule::with(['classSubject.subject', 'classSubject.schoolClass'])
            ->whereIn('class_subject_id', $classSubjectIds)
            ->get();

        // Turmas únicas que este professor leciona
        $classes = $schedules
            ->map(fn($s) => $s->classSubject?->schoolClass)
            ->filter()
            ->unique('id')
            ->values();

        $days = [
            ['id' => 1, 'name' => 'Segunda-feira', 'short' => 'SEG'],
            ['id' => 2, 'name' => 'Terça-feira',   'short' => 'TER'],
            ['id' => 3, 'name' => 'Quarta-feira',  'short' => 'QUA'],
            ['id' => 4, 'name' => 'Quinta-feira',  'short' => 'QUI'],
            ['id' => 5, 'name' => 'Sexta-feira',   'short' => 'SEX'],
        ];

        return Inertia::render('Teacher/Schedule', [
            'teacher'   => $teacher->only('id', 'name', 'email'),
            'schedules' => $schedules,
            'classes'   => $classes,
            'days'      => $days,
        ]);
    }
}
