<?php

namespace App\Helpers;

class DateHelper
{
    /**
     * Formata data para padrão BR (dd/mm/aaaa).
     */
    public static function formatarData(?\DateTimeInterface $data): ?string
    {
        return $data?->format('d/m/Y');
    }

    /**
     * Formata hora para padrão BR (HH:mm).
     */
    public static function formatarHora(?string $hora): ?string
    {
        if (!$hora) return null;
        return substr($hora, 0, 5);
    }

    /**
     * Formata data e hora para padrão BR.
     */
    public static function formatarDataHora(?\DateTimeInterface $dataHora): ?string
    {
        return $dataHora?->format('d/m/Y H:i');
    }

    /**
     * Retorna o nome do dia da semana em PT-BR.
     */
    public static function nomeDiaSemana(int $diaSemana): string
    {
        $dias = [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
        ];

        return $dias[$diaSemana] ?? '';
    }
}
