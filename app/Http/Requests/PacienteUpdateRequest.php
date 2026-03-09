<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PacienteUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:14',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'data_nascimento' => 'nullable|date',
            'observacoes' => 'nullable|string',
            'status' => 'nullable|string|in:ativo,bloqueado,alta',
            'senha' => 'nullable|string|min:6',
        ];
    }
}
