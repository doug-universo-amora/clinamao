<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandlePortalInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        $paciente = Auth::guard('paciente')->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'paciente' => $paciente ? [
                    'id' => $paciente->id,
                    'nome' => $paciente->nome,
                    'email' => $paciente->email,
                    'cpf' => $paciente->cpf,
                    'telefone' => $paciente->telefone,
                    'cliente_id' => $paciente->cliente_id,
                    'senha_provisoria' => $paciente->senha_provisoria,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
