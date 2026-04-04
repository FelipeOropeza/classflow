<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\ClassSubject;
use App\Models\Term;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'Acesso às avaliações é exclusivo do perfil docente.');
        }

        $user = Auth::user();

        // Buscar vínculos (Turmas e Matérias) do professor
        $links = ClassSubject::with(['schoolClass', 'subject'])
            ->where('teacher_id', $user->id)
            ->get();

        $selectedLinkId = $request->input('link_id');
        $selectedTermId = $request->input('term_id');
        
        $assessments = [];
        $selectedLink = null;
        $terms = Term::orderBy('name')->get();

        if ($selectedLinkId) {
            $selectedLink = ClassSubject::with(['schoolClass', 'subject'])
                ->where('id', $selectedLinkId)
                ->where('teacher_id', $user->id)
                ->firstOrFail();

            $query = Assessment::with('term')
                ->where('class_subject_id', $selectedLinkId);

            if ($selectedTermId) {
                $query->where('term_id', $selectedTermId);
            }

            $assessments = $query->orderBy('created_at', 'desc')->get();
        }

        return Inertia::render('Assessments/Index', [
            'links' => $links,
            'terms' => $terms,
            'assessments' => $assessments,
            'filters' => $request->only(['link_id', 'term_id']),
            'selectedLink' => $selectedLink
        ]);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'Somente professores podem planejar avaliações.');
        }

        $request->validate([
            'class_subject_id' => 'required|exists:class_subject,id',
            'term_id' => 'required|exists:terms,id',
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0|max:100',
            'date' => 'nullable|date'
        ]);

        Assessment::create([
            'class_subject_id' => $request->class_subject_id,
            'term_id' => $request->term_id,
            'name' => $request->name,
            'max_score' => $request->max_score,
            'date' => $request->date
        ]);

        return back()->with('success', 'Avaliação planejada com sucesso!');
    }
}
