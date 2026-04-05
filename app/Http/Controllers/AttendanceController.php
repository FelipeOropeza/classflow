<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use App\Models\SchoolEvent;
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
        $existingAttendance = collect([]);

        if ($selectedLinkId) {
            $link = ClassSubject::with(['schoolClass', 'subject'])->findOrFail($selectedLinkId);
            
            // Buscar alunos matriculados nesta turma
            $enrollments = Enrollment::with('student')
                ->where('class_id', $link->class_id)
                ->get();

            // Buscar frequência já lançada vinculada ao Diário de Classe desta aula/data
            $diary = \App\Models\ClassDiary::where('class_subject_id', $link->id)->where('diary_date', $date)->first();
            if ($diary) {
                $existingAttendance = Attendance::where('class_diary_id', $diary->id)
                    ->get()
                    ->keyBy('enrollment_id');
            }

            // Verificar se já está trancada (basta um registro estar locked para travar a aula do dia)
            $isLocked = $existingAttendance->where('is_locked', true)->count() > 0;
        }

        // Verificar Feriado/Recesso
        $holiday = SchoolEvent::where('event_date', $date)
            ->whereIn('type', ['holiday', 'recess'])
            ->first();

        // Buscar Diário se existir
        $diary = null;
        if ($selectedLinkId) {
            $diary = \App\Models\ClassDiary::where('class_subject_id', $selectedLinkId)
                ->where('diary_date', $date)
                ->first();
        }

        return Inertia::render('Attendance/Index', [
            'links' => $links,
            'selectedLink' => $link,
            'date' => $date,
            'enrollments' => $enrollments,
            'existingAttendance' => $existingAttendance,
            'diary' => $diary,
            'holiday' => $holiday,
            'isLocked' => $isLocked,
            'canEdit' => !$isLocked && !$holiday && $date === date('Y-m-d') 
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
            'taught_content' => 'required|string|min:10',
            'homework' => 'nullable|string',
            'attendance' => 'required|array',
            'attendance.*.enrollment_id' => 'required|exists:enrollments,id',
            'attendance.*.status' => 'required|in:present,absent,late',
            'attendance.*.observation' => 'nullable|string',
        ], [
            'taught_content.required' => 'O conteúdo ministrado é obrigatório para fechar o diário.',
            'taught_content.min' => 'Descreva o conteúdo com pelo menos 10 caracteres.',
            'attendance.required' => 'A lista de presença não foi carregada corretamente.',
        ]);

        $link = ClassSubject::findOrFail($validated['link_id']);
        $date = $validated['date'];

        // Regra de Ouro: Feriado Bloqueia Tudo
        $holiday = SchoolEvent::where('event_date', $date)->whereIn('type', ['holiday', 'recess'])->exists();
        if ($holiday) {
             return back()->withErrors(['message' => 'Impossível realizar chamada em feriado ou recesso escolar.']);
        }

        // Regra: Não pode mudar se já foi enviado (locked)
        $diary = \App\Models\ClassDiary::where('class_subject_id', $link->id)->where('diary_date', $date)->first();
        $alreadyLocked = $diary ? Attendance::where('class_diary_id', $diary->id)->where('is_locked', true)->exists() : false;

        if ($alreadyLocked) {
            return back()->withErrors(['message' => 'Esta chamada já foi fechada e não pode ser alterada.']);
        }

        // Regra: Chamada apenas no dia da aula (hoje)
        if ($date !== date('Y-m-d')) {
             return back()->withErrors(['message' => 'Só é permitido realizar a chamada no dia atual da aula.']);
        }

        // Criar ou recuperar o Diário de Classe (Entidade Raiz da Presença)
        $schoolClass = $link->schoolClass;
        $term = \App\Models\Term::where('academic_year_id', $schoolClass->academic_year_id)
            ->where('start_date', '<=', $date)->where('end_date', '>=', $date)->first();
        $termId = $term ? $term->id : \App\Models\Term::where('academic_year_id', $schoolClass->academic_year_id)->first()->id;

        $diary = \App\Models\ClassDiary::updateOrCreate(
            ['class_subject_id' => $link->id, 'diary_date' => $date],
            [
                'term_id' => $termId,
                'taught_content' => $validated['taught_content'],
                'homework' => $validated['homework'],
            ]
        );

        foreach ($validated['attendance'] as $item) {
            Attendance::updateOrCreate(
                [
                    'enrollment_id' => $item['enrollment_id'],
                    'class_diary_id' => $diary->id,
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
