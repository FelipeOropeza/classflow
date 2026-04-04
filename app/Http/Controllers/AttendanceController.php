<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'Acesso restrito ao diário de classe do professor.');
        }

        $user = Auth::user();
        
        // Recupera links de aulas (se professor, apenas os dele)
        $links = ClassSubject::with(['schoolClass', 'subject', 'teacher'])
            ->when($user->role === 'teacher', function($q) use ($user) {
                $q->where('teacher_id', $user->id);
            })
            ->get();

        $selectedLinkId = $request->link_id;
        $date = $request->date ?? date('Y-m-d');
        
        $link = null;
        $enrollments = [];
        $isLocked = false;
        $existingAttendance = [];

        if ($selectedLinkId) {
            $link = ClassSubject::with(['schoolClass', 'subject'])->findOrFail($selectedLinkId);
            
            // Buscar alunos matriculados nesta turma
            $enrollments = Enrollment::with('student')
                ->where('class_id', $link->class_id)
                ->get();

            // Buscar frequência já lançada para esta aula/data
            $existingAttendance = Attendance::where('subject_id', $link->subject_id)
                ->where('date', $date)
                ->get()
                ->keyBy('enrollment_id');

            // Verificar se já está trancada (basta um registro estar locked para travar a aula do dia)
            $isLocked = $existingAttendance->where('is_locked', true)->count() > 0;
        }

        return Inertia::render('Attendance/Index', [
            'links' => $links,
            'selectedLink' => $link,
            'date' => $date,
            'enrollments' => $enrollments,
            'existingAttendance' => $existingAttendance,
            'isLocked' => $isLocked,
            'canEdit' => !$isLocked && $date === date('Y-m-d') // Só edita se hoje e não trancado
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'Somente professores podem realizar o fechamento da chamada.');
        }

        $validated = $request->validate([
            'link_id' => 'required|exists:class_subject,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.enrollment_id' => 'required|exists:enrollments,id',
            'attendance.*.status' => 'required|in:present,absent,late',
        ]);

        $link = ClassSubject::findOrFail($validated['link_id']);
        $date = $validated['date'];

        // Regra: Não pode mudar se já foi enviado (locked)
        $alreadyLocked = Attendance::where('subject_id', $link->subject_id)
            ->where('date', $date)
            ->where('is_locked', true)
            ->exists();

        if ($alreadyLocked) {
            return back()->withErrors(['message' => 'Esta chamada já foi fechada e não pode ser alterada.']);
        }

        // Regra: Chamada apenas no dia da aula (hoje)
        if ($date !== date('Y-m-d')) {
             return back()->withErrors(['message' => 'Só é permitido realizar a chamada no dia atual da aula.']);
        }

        foreach ($validated['attendance'] as $item) {
            Attendance::updateOrCreate(
                [
                    'enrollment_id' => $item['enrollment_id'],
                    'subject_id' => $link->subject_id,
                    'date' => $date,
                ],
                [
                    'status' => $item['status'],
                    'observation' => $item['observation'] ?? null,
                    'is_locked' => true, // Tranca IMEDIATAMENTE após o envio
                ]
            );
        }

        return back()->with('success', 'Chamada realizada e enviada com sucesso! (Registro Trancado)');
    }
}
