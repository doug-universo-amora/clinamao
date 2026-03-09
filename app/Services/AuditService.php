<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Registra uma ação de auditoria.
     */
    public static function registrar(
        string $acao,
        string $entidade,
        ?int $entidadeId = null,
        ?array $dadosAnteriores = null,
        ?array $dadosNovos = null
    ): AuditLog {
        return AuditLog::create([
            'cliente_id' => Auth::check() ? Auth::user()->cliente_id : null,
            'user_id' => Auth::id(),
            'acao' => $acao,
            'entidade' => $entidade,
            'entidade_id' => $entidadeId,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'ip' => Request::ip(),
        ]);
    }
}
