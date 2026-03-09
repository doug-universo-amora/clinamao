<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Enums\AgendamentoStatusEnum;
use App\Models\Agendamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PortalAgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $paciente = Auth::guard('paciente')->user();
        $tab = $request->input('tab', 'proximas');

        $query = Agendamento::with(['profissional.user', 'sala'])
            ->where('paciente_id', $paciente->id);

        if ($tab === 'proximas') {
            $query->where('data', '>=', now()->toDateString())
                  ->whereNotIn('status', [
                      AgendamentoStatusEnum::CANCELADO->value,
                      AgendamentoStatusEnum::FALTOU->value,
                      AgendamentoStatusEnum::FINALIZADO->value,
                  ])
                  ->orderBy('data')
                  ->orderBy('hora_inicio');
        } else {
            // histórico — todas as passadas + finalizadas/canceladas
            $query->where(function ($q) {
                $q->where('data', '<', now()->toDateString())
                  ->orWhereIn('status', [
                      AgendamentoStatusEnum::FINALIZADO->value,
                      AgendamentoStatusEnum::CANCELADO->value,
                      AgendamentoStatusEnum::FALTOU->value,
                  ]);
            })
            ->orderByDesc('data')
            ->orderByDesc('hora_inicio');
        }

        $agendamentos = $query->paginate(10)->withQueryString();

        return Inertia::render('Portal/Agendamentos', [
            'agendamentos' => $agendamentos,
            'tab' => $tab,
        ]);
    }

    public function show(int $id)
    {
        $paciente = Auth::guard('paciente')->user();

        $agendamento = Agendamento::with([
            'profissional.user',
            'sala',
            'historicoReagendamentos',
        ])
        ->where('paciente_id', $paciente->id)
        ->findOrFail($id);

        return Inertia::render('Portal/AgendamentoShow', [
            'agendamento' => $agendamento,
        ]);
    }
}
