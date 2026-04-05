<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Term;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class GuardianController extends Controller
{
    public function reportCard(Request $request)
    {
        $user = Auth::user();
        
        // Buscar filhos vinculados a este responsável
        $students = Student::where('guardian_id', $user->id)->get();
        if ($students->isEmpty()) {
            return Inertia::render('Guardian/ReportCard', ['students' => [], 'report' => []]);
        }

        $selectedStudentId = $request->input('student_id', $students->first()->id);
        $student = Student::with(['enrollments.schoolClass'])->findOrFail($selectedStudentId);
        
        $enrollment = $student->enrollments()->latest()->first();
        if (!$enrollment) {
            return Inertia::render('Guardian/ReportCard', ['students' => $students, 'report' => []]);
        }

        // Buscar todas as disciplinas da turma
        $classSubjects = \App\Models\ClassSubject::with('subject')
            ->where('class_id', $enrollment->class_id)
            ->get();

        // Buscar todos os bimestres do ano
        $terms = Term::where('academic_year_id', $enrollment->academic_year_id)
            ->orderBy('start_date')
            ->get();

        $report = [];

        foreach ($classSubjects as $cs) {
            $subjectData = [
                'name' => $cs->subject->name,
                'terms' => [],
                'final_average' => 0,
                'total_absences' => Attendance::where('enrollment_id', $enrollment->id)
                    ->whereHas('classDiary', function($q) use ($cs) {
                        $q->where('class_subject_id', $cs->id);
                    })
                    ->where('status', 'absent')
                    ->count()
            ];

            $sumOfAverages = 0;
            $termsWithGrades = 0;

            foreach ($terms as $term) {
                // Média do bimestre
                $grades = Grade::where('enrollment_id', $enrollment->id)
                    ->where('subject_id', $cs->subject_id)
                    ->where('term_id', $term->id)
                    ->get();

                $average = $grades->count() > 0 ? $grades->avg('score') : null;
                
                $subjectData['terms'][] = [
                    'term_name' => $term->name,
                    'average' => $average !== null ? round($average, 1) : '-'
                ];

                if ($average !== null) {
                    $sumOfAverages += $average;
                    $termsWithGrades++;
                }
            }

            $subjectData['final_average'] = $termsWithGrades > 0 ? round($sumOfAverages / 4, 1) : '-';
            
            $report[] = $subjectData;
        }

        return Inertia::render('Guardian/ReportCard', [
            'students' => $students,
            'report' => $report,
            'selectedStudent' => $student,
            'terms' => $terms
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $student = Student::with(['enrollments.schoolClass', 'enrollments.academicYear'])->where('guardian_id', $user->id)->findOrFail($request->student_id);
        
        $enrollment = $student->enrollments()->latest()->first();
        if (!$enrollment) {
            abort(404, 'Matrícula não encontrada.');
        }

        $classSubjects = \App\Models\ClassSubject::with('subject')->where('class_id', $enrollment->class_id)->get();
        $terms = Term::where('academic_year_id', $enrollment->academic_year_id)->orderBy('start_date')->get();

        $report = [];
        foreach ($classSubjects as $cs) {
            $subjectData = [
                'name' => $cs->subject->name,
                'terms' => [],
                'final_average' => 0,
                'total_absences' => Attendance::where('enrollment_id', $enrollment->id)
                    ->whereHas('classDiary', function($q) use ($cs) {
                        $q->where('class_subject_id', $cs->id);
                    })
                    ->where('status', 'absent')
                    ->count()
            ];

            $sumOfAverages = 0;
            $termsWithGrades = 0;

            foreach ($terms as $term) {
                $grades = Grade::where('enrollment_id', $enrollment->id)
                    ->where('subject_id', $cs->subject_id)
                    ->where('term_id', $term->id)
                    ->get();

                $average = $grades->count() > 0 ? $grades->avg('score') : null;
                $subjectData['terms'][] = [
                    'term_name' => $term->name,
                    'average' => $average !== null ? round($average, 1) : '-'
                ];

                if ($average !== null) {
                    $sumOfAverages += $average;
                    $termsWithGrades++;
                }
            }

            $subjectData['final_average'] = $termsWithGrades > 0 ? round($sumOfAverages / 4, 1) : '-';
            $report[] = $subjectData;
        }

        $pdf = Pdf::loadView('pdf.report_card', [
            'student' => $student,
            'className' => $enrollment->schoolClass->name,
            'year' => $enrollment->academicYear->name ?? date('Y'),
            'terms' => $terms,
            'report' => $report
        ]);

        return $pdf->stream('boletim_' . \Illuminate\Support\Str::slug($student->name) . '.pdf');
    }

    public function attendance(Request $request)
    {
        $user = Auth::user();
        
        // Buscar filhos vinculados a este responsável
        $students = Student::where('guardian_id', $user->id)->get();
        if ($students->isEmpty()) {
            return Inertia::render('Guardian/Attendance', ['students' => [], 'attendance' => []]);
        }

        $selectedStudentId = $request->input('student_id', $students->first()->id);
        $student = Student::with(['enrollments.schoolClass'])->findOrFail($selectedStudentId);
        
        $enrollment = $student->enrollments()->latest()->first();
        if (!$enrollment) {
            return Inertia::render('Guardian/Attendance', ['students' => $students, 'attendance' => []]);
        }

        $classSubjects = \App\Models\ClassSubject::with('subject')
            ->where('class_id', $enrollment->class_id)
            ->get();

        $terms = Term::where('academic_year_id', $enrollment->academic_year_id)
            ->orderBy('start_date')
            ->get();

        $attendanceReport = [];

        foreach ($classSubjects as $cs) {
            $subjectData = [
                'subject_name' => $cs->subject->name,
                'terms' => [],
                'total_absences' => 0
            ];

            foreach ($terms as $term) {
                $absences = Attendance::where('enrollment_id', $enrollment->id)
                    ->whereHas('classDiary', function($q) use ($cs, $term) {
                        $q->where('class_subject_id', $cs->id)->where('term_id', $term->id);
                    })
                    ->where('status', 'absent')
                    ->count();

                $subjectData['terms'][] = [
                    'term_id' => $term->id,
                    'term_name' => $term->name,
                    'absences' => $absences
                ];

                $subjectData['total_absences'] += $absences;
            }
            
            $attendanceReport[] = $subjectData;
        }

        return Inertia::render('Guardian/Attendance', [
            'students' => $students,
            'attendanceReport' => $attendanceReport,
            'selectedStudent' => $student,
            'terms' => $terms
        ]);
    }
}
