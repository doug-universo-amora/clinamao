<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Métodos HTTP que devem ser auditados (mutações).
     */
    protected array $auditMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (in_array($request->method(), $this->auditMethods) && auth()->check()) {
            $this->registrarAuditoria($request, $response);
        }

        return $response;
    }

    protected function registrarAuditoria(Request $request, Response $response): void
    {
        try {
            AuditLog::create([
                'cliente_id' => auth()->user()->cliente_id ?? null,
                'user_id' => auth()->id(),
                'acao' => strtolower($request->method()),
                'entidade' => $request->path(),
                'entidade_id' => null,
                'dados_anteriores' => null,
                'dados_novos' => $request->except(['password', 'senha', '_token']),
                'ip' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            // Silenciosamente falha — auditoria não pode quebrar a aplicação
            report($e);
        }
    }
}
