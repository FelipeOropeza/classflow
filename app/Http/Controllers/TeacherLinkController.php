<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeacherLinkController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('active', true)->first();

        // Links de aulas (Vínculos)
        $links = ClassSubject::with(['schoolClass', 'subject', 'teacher'])
            ->whereHas('schoolClass', function($query) use ($activeYear) {
                if ($activeYear) {
                    $query->where('academic_year_id', $activeYear->id);
                }
            })
            ->latest()
            ->get();

        return Inertia::render('Teachers/AcademicLinks', [
            'links' => $links,
            'classes' => SchoolClass::where('academic_year_id', $activeYear?->id)->get(),
            'subjects' => Subject::all(),
            'teachers' => User::where('role', 'teacher')->with('subjects')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        // Evitar duplicatas (mesma turma + mesma disciplina)
        $exists = ClassSubject::where('class_id', $validated['class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['message' => 'Este vínculo já existe para esta turma e disciplina.']);
        }

        ClassSubject::create($validated);

        return redirect()->route('academic-links.index');
    }

    public function destroy(ClassSubject $link)
    {
        $link->delete();
        return redirect()->route('academic-links.index');
    }
}
