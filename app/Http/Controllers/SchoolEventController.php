<?php

namespace App\Http\Controllers;

use App\Models\SchoolEvent;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class SchoolEventController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $activeYear = AcademicYear::where('active', true)->first();
        
        return Inertia::render('Events/Index', [
            'events' => SchoolEvent::where('academic_year_id', $activeYear?->id)
                ->orderBy('event_date', 'asc')
                ->get(),
            'activeYearId' => $activeYear?->id
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'type' => 'required|string',
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        SchoolEvent::create($validated);

        return redirect()->back()->with('success', 'Evento criado com sucesso!');
    }

    public function destroy(SchoolEvent $schoolEvent)
    {
        $this->authorizeAdmin();
        $schoolEvent->delete();
        return redirect()->back()->with('success', 'Evento removido!');
    }

    private function authorizeAdmin()
    {
        if (!in_array(Auth::user()->role, ['admin', 'director'])) {
            abort(403, 'Acesso restrito à diretoria.');
        }
    }
}
