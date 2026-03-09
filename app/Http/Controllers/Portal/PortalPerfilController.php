<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class PortalPerfilController extends Controller
{
    public function edit()
    {
        $paciente = Auth::guard('paciente')->user();

        return Inertia::render('Portal/Perfil', [
            'paciente' => [
                'id' => $paciente->id,
                'nome' => $paciente->nome,
                'cpf' => $paciente->cpf,
                'email' => $paciente->email,
                'telefone' => $paciente->telefone,
                'data_nascimento' => $paciente->data_nascimento?->format('Y-m-d'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $paciente = Auth::guard('paciente')->user();

        $validated = $request->validate([
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
        ]);

        $paciente->update($validated);

        return redirect()->route('portal.perfil')
            ->with('success', 'Perfil atualizado com sucesso.');
    }

    public function alterarSenha(Request $request)
    {
        $paciente = Auth::guard('paciente')->user();

        $request->validate([
            'senha_atual' => 'required|string',
            'nova_senha' => ['required', 'string', 'confirmed', Password::min(6)],
        ]);

        if (!Hash::check($request->senha_atual, $paciente->senha)) {
            return back()->withErrors(['senha_atual' => 'A senha atual está incorreta.']);
        }

        $paciente->update([
            'senha' => $request->nova_senha,
            'senha_provisoria' => false,
        ]);

        return redirect()->route('portal.perfil')
            ->with('success', 'Senha alterada com sucesso.');
    }
}
