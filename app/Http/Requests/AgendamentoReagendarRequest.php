<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgendamentoReagendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i|after:hora_inicio',
        ];
    }

    public function messages(): array
    {
        return [
            'data.required' => 'Informe a nova data.',
            'data.after_or_equal' => 'A data não pode ser no passado.',
            'hora_inicio.required' => 'Informe o novo horário.',
        ];
    }
}
