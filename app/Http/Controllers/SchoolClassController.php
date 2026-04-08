<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SchoolClassController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('active', true)->first();
        
        $classes = SchoolClass::withCount('enrollments')
            ->where('academic_year_id', $activeYear->id ?? null)
            ->orderBy('name')
            ->get();

        return Inertia::render('Classes/Index', [
            'classes' => $classes,
            'activeYear' => $activeYear?->name ?? 'Nenhum',
            'academic_year_id' => $activeYear->id ?? null
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        SchoolClass::create($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Turma criada com sucesso!');
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Turma removida com sucesso!');
    }

    public function toggleActive(SchoolClass $class)
    {
        $class->update(['is_active' => !$class->is_active]);

        return redirect()->route('classes.index')
            ->with('success', 'Status da turma atualizado com sucesso!');
    }
}
