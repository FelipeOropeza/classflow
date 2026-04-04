<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TermController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('active', true)->first();
        if (!$activeYear) {
            return back()->withErrors(['message' => 'Nenhum ano letivo ativo encontrado.']);
        }

        $terms = Term::where('academic_year_id', $activeYear->id)
            ->orderBy('start_date')
            ->get();

        return Inertia::render('AcademicYears/Terms', [
            'terms' => $terms,
            'activeYear' => $activeYear
        ]);
    }

    public function update(Request $request, Term $term)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $term->update($validated);

        return back()->with('success', "Período do {$term->name} atualizado com sucesso!");
    }
}
