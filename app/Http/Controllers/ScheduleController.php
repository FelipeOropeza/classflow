<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Schedule;
use App\Models\ClassSubject;
use App\Services\ClassScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    protected ClassScheduleService $scheduleService;

    public function __construct(ClassScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Exibir o quadro de horários de uma turma.
     */
    public function index(SchoolClass $schoolClass)
    {
        $schedules = Schedule::with(['classSubject.subject', 'classSubject.teacher'])
            ->whereHas('classSubject', function ($query) use ($schoolClass) {
                $query->where('class_id', $schoolClass->id);
            })
            ->get();

        $classSubjects = ClassSubject::with(['subject', 'teacher'])
            ->where('class_id', $schoolClass->id)
            ->get();

        return Inertia::render('Schedules/Index', [
            'schoolClass' => $schoolClass,
            'schedules' => $schedules,
            'classSubjects' => $classSubjects
        ]);
    }

    /**
     * Alocar uma aula manualmente.
     */
    public function store(Request $request, SchoolClass $schoolClass)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        abort_unless($user && $user->isAdmin(), 403, 'Apenas administradores podem modificar o quadro de horários.');

        $validated = $request->validate([
            'class_subject_id' => 'required|exists:class_subject,id',
            'day_of_week' => 'required|integer|min:1|max:5',
            'period' => 'required|integer|min:1|max:5',
        ]);

        try {
            $this->scheduleService->allocateManual(
                $validated['class_subject_id'],
                $validated['day_of_week'],
                $validated['period']
            );

            return back()->with('success', 'Aula alocada com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['schedule' => $e->getMessage()]);
        }
    }

    /**
     * Gerar horário automaticamente.
     */
    public function generate(Request $request, SchoolClass $schoolClass)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        abort_unless($user && $user->isAdmin(), 403, 'Apenas administradores podem modificar o quadro de horários.');

        try {
            $this->scheduleService->generateAutomaticSchedule($schoolClass->id);
            return back()->with('success', 'Quadro de horários gerado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['schedule' => 'Erro ao gerar: ' . $e->getMessage()]);
        }
    }
}
