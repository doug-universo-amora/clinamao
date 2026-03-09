<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PortalPacienteMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('paciente')->check()) {
            return redirect()->route('portal.login');
        }

        $paciente = Auth::guard('paciente')->user();

        // Verificar se o paciente está ativo
        if ($paciente->status->value !== 'ativo') {
            Auth::guard('paciente')->logout();
            return redirect()->route('portal.login')
                ->withErrors(['cpf' => 'Sua conta está inativa. Entre em contato com a clínica.']);
        }

        return $next($request);
    }
}
