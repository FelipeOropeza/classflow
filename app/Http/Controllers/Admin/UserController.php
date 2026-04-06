<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::where('role', 'guardian')->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|min:3|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'              => 'O nome do responsável é obrigatório.',
            'name.min'                   => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max'                   => 'O nome não pode ultrapassar 255 caracteres.',
            'email.required'             => 'O e-mail é obrigatório.',
            'email.email'                => 'Informe um endereço de e-mail válido.',
            'email.unique'               => 'Este e-mail já está cadastrado no sistema.',
            'password.required'          => 'A senha é obrigatória.',
            'password.min'               => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed'         => 'As senhas não coincidem.',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'guardian',
        ]);

        return back()->with('success', 'Responsável registrado com sucesso!');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->withErrors(['geral' => 'Não é possível remover um administrador.']);
        }

        $user->delete();
        return back()->with('success', 'Responsável removido com sucesso.');
    }
}
