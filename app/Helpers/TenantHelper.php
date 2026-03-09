<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class TenantHelper
{
    /**
     * Obtém o cliente_id do usuário autenticado.
     */
    public static function getClienteId(): ?int
    {
        if (Auth::check() && Auth::user()->cliente_id) {
            return Auth::user()->cliente_id;
        }

        return null;
    }

    /**
     * Define o cliente_id para o contexto do Spatie Permission (teams).
     */
    public static function setPermissionsTeamId(?int $clienteId): void
    {
        setPermissionsTeamId($clienteId);
    }
}
