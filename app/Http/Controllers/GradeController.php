<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Enrollment;
use App\Models\ClassSubject;
use App\Models\Assessment;
use App\Models\Term;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'O lançamento de notas é um ato pedagógico exclusivo do professor.');
        }

        $user = Auth::user();
        
        // Buscar vínculos do professor vinculados a turmas ATIVAS
        $links = ClassSubject::with(['schoolClass', 'subject'])
            ->where('teacher_id', $user->id)
            ->whereHas('schoolClass', function($q) {
                $q->where('is_active', true);
            })
            ->get();

        $selectedLinkId = $request->input('link_id');
        $selectedAssessmentId = $request->input('assessment_id');
        
        $students = [];
        $selectedLink = null;
        $selectedAssessment = null;
        $assessments = [];

        if ($selectedLinkId) {
            $selectedLink = ClassSubject::with(['schoolClass', 'subject'])
                ->where('id', $selectedLinkId)
                ->where('teacher_id', $user->id)
                ->firstOrFail();

            // Buscar avaliações criadas para este vínculo
            $assessments = Assessment::where('class_subject_id', $selectedLinkId)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($selectedAssessmentId) {
                $selectedAssessment = Assessment::findOrFail($selectedAssessmentId);

                // Buscar alunos matriculados na turma com suas notas para ESTA avaliação específica
                $students = Enrollment::with(['student', 'grades' => function($q) use ($selectedAssessmentId) {
                    $q->where('assessment_id', $selectedAssessmentId);
                }])
                ->where('class_id', $selectedLink->class_id)
                ->get()
                ->map(function($enrollment) {
                    $grade = $enrollment->grades->first();
                    return [
                        'id' => $enrollment->id,
                        'student_name' => $enrollment->student->name,
                        'registration' => $enrollment->student->registration,
                        'grade_id' => $grade ? $grade->id : null,
                        'score' => $grade ? $grade->score : null,
                    ];
                });
            }
        }

        return Inertia::render('Grades/Index', [
            'links' => $links,
            'assessments' => $assessments,
            'students' => $students,
            'filters' => $request->only(['link_id', 'assessment_id']),
            'selectedLink' => $selectedLink,
            'selectedAssessment' => $selectedAssessment
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'Somente professores podem registrar notas no sistema.');
        }

        $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'grades' => 'required|array',
            'grades.*.enrollment_id' => 'required|exists:enrollments,id',
            'grades.*.score' => 'nullable|numeric|min:0|max:100',
        ]);

        $assessment = Assessment::with('classSubject.schoolClass')->findOrFail($request->assessment_id);
 
        if (!$assessment->classSubject->schoolClass->is_active) {
            return back()->withErrors(['message' => 'Náo é permitido lançar notas para uma turma desativada.']);
        }

        foreach ($request->grades as $gradeData) {
            if ($gradeData['score'] === null || $gradeData['score'] === '') continue;

            Grade::updateOrCreate(
                [
                    'enrollment_id' => $gradeData['enrollment_id'],
                    'assessment_id' => $request->assessment_id,
                ],
                [
                    'subject_id' => $assessment->classSubject->subject_id,
                    'term_id' => $assessment->term_id,
                    'score' => $gradeData['score'],
                    'max_score' => $assessment->max_score,
                ]
            );
        }

        return back()->with('success', 'Notas lançadas com sucesso!');
    }
}
