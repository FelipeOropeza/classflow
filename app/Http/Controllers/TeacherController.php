<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class TeacherController extends Controller
{
    public function index()
    {
        return Inertia::render('Teachers/Index', [
            'teachers' => User::where('role', 'teacher')->with('subjects')->latest()->get(),
            'subjects' => \App\Models\Subject::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $teacher = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
        ]);

        if (!empty($validated['subject_ids'])) {
            $teacher->subjects()->sync($validated['subject_ids']);
        }

        return redirect()->route('teachers.index');
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $teacher->id,
            'password' => 'nullable|string|min:8',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $teacher->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (isset($validated['subject_ids'])) {
            $teacher->subjects()->sync($validated['subject_ids']);
        }

        if ($request->filled('password')) {
            $teacher->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('teachers.index');
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect()->route('teachers.index');
    }
}
