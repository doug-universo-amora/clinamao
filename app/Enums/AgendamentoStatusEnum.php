<?php

namespace App\Enums;

enum AgendamentoStatusEnum: string
{
    case AGENDADO = 'agendado';
    case CONFIRMADO = 'confirmado';
    case EM_ATENDIMENTO = 'em_atendimento';
    case FINALIZADO = 'finalizado';
    case FALTOU = 'faltou';
    case CANCELADO = 'cancelado';
    case REAGENDADO = 'reagendado';

    public function label(): string
    {
        return match ($this) {
            self::AGENDADO => 'Agendado',
            self::CONFIRMADO => 'Confirmado',
            self::EM_ATENDIMENTO => 'Em Atendimento',
            self::FINALIZADO => 'Finalizado',
            self::FALTOU => 'Faltou',
            self::CANCELADO => 'Cancelado',
            self::REAGENDADO => 'Reagendado',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::AGENDADO => '#3B82F6',       // blue
            self::CONFIRMADO => '#10B981',      // green
            self::EM_ATENDIMENTO => '#F59E0B',  // amber
            self::FINALIZADO => '#6B7280',      // gray
            self::FALTOU => '#EF4444',          // red
            self::CANCELADO => '#DC2626',       // dark red
            self::REAGENDADO => '#8B5CF6',      // purple
        };
    }
}
