<?php

namespace App\Enums;

enum PacienteStatusEnum: string
{
    case ATIVO = 'ativo';
    case BLOQUEADO = 'bloqueado';
    case ALTA = 'alta';

    public function label(): string
    {
        return match ($this) {
            self::ATIVO => 'Ativo',
            self::BLOQUEADO => 'Bloqueado',
            self::ALTA => 'Alta',
        };
    }
}
