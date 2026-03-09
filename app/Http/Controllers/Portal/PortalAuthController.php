<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PortalAuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::guard('paciente')->check()) {
            return redirect()->route('portal.dashboard');
        }

        return Inertia::render('Portal/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string',
            'senha' => 'required|string',
        ]);

        $cpf = $request->input('cpf');
        $senha = $request->input('senha');

        // Buscar paciente pelo CPF
        $paciente = \App\Models\Paciente::where('cpf', $cpf)->first();

        if (!$paciente) {
            return back()->withErrors(['cpf' => 'CPF não encontrado.']);
        }

        if ($paciente->status->value !== 'ativo') {
            return back()->withErrors(['cpf' => 'Sua conta está inativa. Entre em contato com a clínica.']);
        }

        if (!$paciente->senha) {
            return back()->withErrors(['cpf' => 'Senha não cadastrada. Entre em contato com a clínica.']);
        }

        // Tentar autenticação via guard
        $credentials = [
            'cpf' => $cpf,
            'password' => $senha,
        ];

        if (Auth::guard('paciente')->attempt($credentials, $request->boolean('lembrar'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('portal.dashboard'));
        }

        return back()->withErrors(['cpf' => 'CPF ou senha incorretos.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('paciente')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
