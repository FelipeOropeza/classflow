<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\ClassDiary;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnrollmentController extends Controller
{
    /**
     * Interface para a Diretoria revisar as notas e aprovações (Conselho de Classe)
     */
    public function review()
    {
        $now = now();
        $start = now()->setMonth(12)->setDay(5)->startOfDay();
        $end = now()->setMonth(12)->setDay(12)->endOfDay();

        if (!$now->between($start, $end)) {
            return redirect()->route('dashboard')->with('error', 'O acesso ao Conselho de Classe só está disponível entre 05/12 e 12/12.');
        }

        $enrollments = Enrollment::with(['student', 'schoolClass'])
            ->where('status', 'active')
            ->get()
            ->map(function($enrollment) {
                // Cálculo de Média (Soma de Notas / 4)
                $grades = Grade::where('enrollment_id', $enrollment->id)->sum('score');
                $finalScore = $grades / 4;

                // Cálculo de Frequência (Faltas / Total de Aulas em TODAS as matérias da turma)
                $totalDiaries = ClassDiary::whereIn('class_subject_id', function($q) use ($enrollment) {
                    $q->select('id')->from('class_subject')->where('class_id', $enrollment->class_id);
                })->count();
                
                $absences = Attendance::where('enrollment_id', $enrollment->id)->where('status', 'absent')->count();
                $attendanceRate = $totalDiaries > 0 ? (1 - ($absences / $totalDiaries)) * 100 : 100;

                return [
                    'id' => $enrollment->id,
                    'student_name' => $enrollment->student->name,
                    'class_name' => $enrollment->schoolClass->name,
                    'final_score' => round($finalScore, 2),
                    'attendance_percentage' => round($attendanceRate, 2),
                    'suggested_status' => ($finalScore >= 5.0 && $attendanceRate >= 50) ? 'approved' : 'retained',
                    'current_status' => $enrollment->status,
                ];
            });

        return Inertia::render('Admin/Enrollments/Review', [
            'enrollments' => $enrollments
        ]);
    }

    /**
     * Atualização Manual do Status (Diretoria/Conselho)
     */
    public function updateStatus(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,approved,retained,completed',
            'final_score' => 'nullable|numeric',
            'attendance_percentage' => 'nullable|numeric',
        ]);

        $enrollment->update($validated);

        return back()->with('success', 'Status do aluno atualizado com sucesso via Conselho de Classe.');
    }
}
