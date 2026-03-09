<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisponibilidadeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profissional_id' => 'required|exists:profissionais,id',
            'dia_semana' => 'required|integer|min:0|max:6',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
            'duracao_consulta' => 'nullable|integer|min:5|max:480',
        ];
    }

    public function messages(): array
    {
        return [
            'hora_fim.after' => 'O horário de término deve ser após o horário de início.',
            'profissional_id.required' => 'Selecione um profissional.',
            'dia_semana.required' => 'Selecione o dia da semana.',
        ];
    }
}
