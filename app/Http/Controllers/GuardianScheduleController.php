<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\SchoolClass;
use Inertia\Inertia;
use Illuminate\Http\Request;

class GuardianScheduleController extends Controller
{
    /**
     * Exibe a grade completa de horários da turma em que o filho está matriculado.
     */
    public function index(Request $request)
    {
        $guardian = $request->user();

        // Localiza o filho pelo guardian_id
        $student = $guardian->students()->with([
            'enrollments.schoolClass' => fn ($q) => $q->with([
                'classSubjects.subject',
            ])
        ])->first();

        // Busca a matrícula ativa (última)
        $enrollment = $student?->enrollments()->latest()->first();

        if (!$enrollment) {
            return Inertia::render('Guardian/Schedule', [
                'schoolClass' => null,
                'schedules'   => [],
                'days'        => [],
            ]);
        }

        $schoolClass = $enrollment->schoolClass;

        $schedules = Schedule::with(['classSubject.subject', 'classSubject.teacher'])
            ->whereHas('classSubject', fn ($q) => $q->where('class_id', $schoolClass->id))
            ->get();

        $days = [
            ['id' => 1, 'name' => 'Segunda-feira', 'short' => 'SEG'],
            ['id' => 2, 'name' => 'Terça-feira',   'short' => 'TER'],
            ['id' => 3, 'name' => 'Quarta-feira',  'short' => 'QUA'],
            ['id' => 4, 'name' => 'Quinta-feira',  'short' => 'QUI'],
            ['id' => 5, 'name' => 'Sexta-feira',   'short' => 'SEX'],
        ];

        return Inertia::render('Guardian/Schedule', [
            'student'     => $student->only('id', 'name'),
            'schoolClass' => $schoolClass->only('id', 'name'),
            'schedules'   => $schedules,
            'days'        => $days,
        ]);
    }
}
