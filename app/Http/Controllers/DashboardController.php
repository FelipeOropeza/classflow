<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\User;
use App\Models\Grade;
use App\Models\ClassSubject;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\SchoolEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeYear = AcademicYear::where('active', true)->first();
        
        $data = [
            'activeYear' => $activeYear?->name ?? 'Nenhum',
            'role' => $user->role,
            'userName' => $user->name,
        ];

        if ($user->role === 'admin' || $user->role === 'director') {
            $data['stats'] = [
                'totalStudents' => Student::count(),
                'activeClasses' => $activeYear ? SchoolClass::where('academic_year_id', $activeYear->id)->count() : 0,
                'totalTeachers' => User::where('role', 'teacher')->count(),
                'newEnrollments' => Enrollment::whereMonth('created_at', now()->month)->count(),
            ];
            // Atividades recentes (últimas notas lançadas no sistema)
            $data['recentActivity'] = Grade::with(['enrollment.student', 'assessment'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($g) => [
                    'student' => $g->enrollment->student->name,
                    'action' => 'Lançamento de Nota',
                    'detail' => "Avaliação: {$g->assessment->name}",
                    'date' => $g->created_at->format('d/m')
                ]);
        } 
        
        elseif ($user->role === 'teacher') {
            $myLinks = ClassSubject::with(['schoolClass', 'subject'])
                ->where('teacher_id', $user->id)
                ->get();
            
            $data['stats'] = [
                'myClasses' => $myLinks->count(),
                'myStudents' => Enrollment::whereIn('class_id', $myLinks->pluck('class_id'))->distinct('student_id')->count(),
                'assessmentsPlanned' => Assessment::whereIn('class_subject_id', $myLinks->pluck('id'))->count(),
            ];
            
            $data['myLinks'] = $myLinks;
            
            // Verificar chamadas pendentes HOJE
            $today = date('Y-m-d');
            $doneTodayIds = \App\Models\ClassDiary::whereIn('class_subject_id', $myLinks->pluck('id'))
                ->where('diary_date', $today)
                ->pluck('class_subject_id')
                ->toArray();
            
            $data['pendingAttendance'] = $myLinks->filter(fn($l) => !in_array($l->id, $doneTodayIds))
                ->map(fn($l) => "{$l->schoolClass->name} - {$l->subject->name}")->values()->toArray();
        }

        elseif ($user->role === 'guardian') {
            // Buscar filhos vinculados (Simulação: pegar alunos onde o guardian_id está na tabela students ou via relação)
            // Aqui vamos assumir que o Guardian vê os alunos matriculados
            $students = Student::where('guardian_id', $user->id)->get();
            
            $data['myChildren'] = $students->map(function($student) {
                $enrollment = Enrollment::where('student_id', $student->id)->latest()->first();
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'class' => $enrollment?->schoolClass->name ?? 'N/A',
                    'attendanceRate' => 95, // Mock por enquanto
                ];
            });

            // Próximas avaliações dos filhos
            $classIds = Enrollment::whereIn('student_id', $students->pluck('id'))->pluck('class_id');
            $data['upcomingAssessments'] = Assessment::with(['classSubject.subject', 'classSubject.schoolClass'])
                ->whereIn('class_subject_id', ClassSubject::whereIn('class_id', $classIds)->pluck('id'))
                ->where('date', '>=', now())
                ->orderBy('date')
                ->take(5)
                ->get();
        }

        // Agenda Escolar - Visível para TODOS
        $data['schoolEvents'] = SchoolEvent::where('academic_year_id', $activeYear?->id)
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get()
            ->map(fn($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'description' => $e->description,
                'day' => date('d', strtotime($e->event_date)),
                'month' => strtoupper(substr(strftime('%b', strtotime($e->event_date)), 0, 3)),
                'type' => $e->type,
                'color' => $e->color_hex
            ]);

        return Inertia::render('Dashboard', $data);
    }
}
