<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Enums\AgendamentoStatusEnum;
use App\Models\Agendamento;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PortalDashboardController extends Controller
{
    public function index()
    {
        $paciente = Auth::guard('paciente')->user();

        // Próximas consultas (agendadas ou confirmadas, a partir de hoje)
        $proximasConsultas = Agendamento::with(['profissional.user', 'sala'])
            ->where('paciente_id', $paciente->id)
            ->where('data', '>=', now()->toDateString())
            ->whereIn('status', [
                AgendamentoStatusEnum::AGENDADO->value,
                AgendamentoStatusEnum::CONFIRMADO->value,
                AgendamentoStatusEnum::REAGENDADO->value,
            ])
            ->orderBy('data')
            ->orderBy('hora_inicio')
            ->limit(5)
            ->get();

        // Última consulta finalizada
        $ultimaConsulta = Agendamento::with(['profissional.user'])
            ->where('paciente_id', $paciente->id)
            ->where('status', AgendamentoStatusEnum::FINALIZADO->value)
            ->orderByDesc('data')
            ->first();

        // Contadores
        $totalConsultas = Agendamento::where('paciente_id', $paciente->id)->count();
        $consultasFuturas = Agendamento::where('paciente_id', $paciente->id)
            ->where('data', '>=', now()->toDateString())
            ->whereNotIn('status', [
                AgendamentoStatusEnum::CANCELADO->value,
                AgendamentoStatusEnum::FALTOU->value,
            ])
            ->count();

        return Inertia::render('Portal/Dashboard', [
            'paciente' => $paciente,
            'proximasConsultas' => $proximasConsultas,
            'ultimaConsulta' => $ultimaConsulta,
            'totalConsultas' => $totalConsultas,
            'consultasFuturas' => $consultasFuturas,
        ]);
    }
}
