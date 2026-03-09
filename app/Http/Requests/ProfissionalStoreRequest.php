<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfissionalStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'especialidade' => 'required|string|max:255',
            'registro_conselho' => 'nullable|string|max:100',
            'duracao_consulta' => 'nullable|integer|min:5|max:480',
            'status' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'O usuário é obrigatório.',
            'user_id.exists' => 'Usuário inválido.',
            'especialidade.required' => 'A especialidade é obrigatória.',
            'duracao_consulta.min' => 'A duração mínima é 5 minutos.',
        ];
    }
}
