<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgendamentoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profissional_id' => 'required|exists:profissionais,id',
            'paciente_id' => 'required|exists:pacientes,id',
            'sala_id' => 'nullable|exists:salas,id',
            'data' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i|after:hora_inicio',
            'observacoes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'profissional_id.required' => 'Selecione um profissional.',
            'paciente_id.required' => 'Selecione um paciente.',
            'data.required' => 'Informe a data do agendamento.',
            'data.after_or_equal' => 'A data não pode ser no passado.',
            'hora_inicio.required' => 'Informe o horário de início.',
        ];
    }
}
