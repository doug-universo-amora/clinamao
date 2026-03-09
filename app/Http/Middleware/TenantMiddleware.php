<?php

namespace App\Http\Middleware;

use App\Helpers\TenantHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $clienteId = auth()->user()->cliente_id;

            // Define o team_id do Spatie Permission
            setPermissionsTeamId($clienteId);
        }

        return $next($request);
    }
}
