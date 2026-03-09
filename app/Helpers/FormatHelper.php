<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Formata CPF (000.000.000-00).
     */
    public static function formatarCpf(?string $cpf): ?string
    {
        if (!$cpf) return null;
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) !== 11) return $cpf;
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    /**
     * Formata CNPJ (00.000.000/0000-00).
     */
    public static function formatarCnpj(?string $cnpj): ?string
    {
        if (!$cnpj) return null;
        $cnpj = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpj) !== 14) return $cnpj;
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }

    /**
     * Formata telefone ((00) 00000-0000 ou (00) 0000-0000).
     */
    public static function formatarTelefone(?string $telefone): ?string
    {
        if (!$telefone) return null;
        $telefone = preg_replace('/\D/', '', $telefone);
        $len = strlen($telefone);

        if ($len === 11) {
            return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 5) . '-' . substr($telefone, 7, 4);
        }
        if ($len === 10) {
            return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6, 4);
        }

        return $telefone;
    }

    /**
     * Remove formatação de CPF/CNPJ/Telefone (apenas dígitos).
     */
    public static function apenasDigitos(?string $valor): ?string
    {
        if (!$valor) return null;
        return preg_replace('/\D/', '', $valor);
    }
}
