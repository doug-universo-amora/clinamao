<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfissionalUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'especialidade' => 'required|string|max:255',
            'registro_conselho' => 'nullable|string|max:100',
            'duracao_consulta' => 'nullable|integer|min:5|max:480',
            'status' => 'boolean',
        ];
    }
}
