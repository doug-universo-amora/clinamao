<?php

namespace App\Services;

use App\Enums\AgendamentoStatusEnum;
use App\Models\Agendamento;
use App\Models\BloqueioAgenda;
use App\Models\Disponibilidade;
use App\Models\Feriado;
use App\Models\HistoricoReagendamento;
use App\Models\Profissional;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgendaService
{
    /**
     * Gera os horários disponíveis para um profissional em uma data.
     */
    public function gerarHorariosDisponiveis(int $profissionalId, string $data): array
    {
        $dataCarbono = Carbon::parse($data);
        $diaSemana = $dataCarbono->dayOfWeek;
        $clienteId = Auth::user()->cliente_id;

        // Verificar feriado
        if (Feriado::where('cliente_id', $clienteId)->whereDate('data', $dataCarbono)->exists()) {
            return [];
        }

        // Obter disponibilidades do profissional para este dia
        $disponibilidades = Disponibilidade::where('profissional_id', $profissionalId)
            ->where('dia_semana', $diaSemana)
            ->get();

        if ($disponibilidades->isEmpty()) {
            return [];
        }

        // Obter bloqueios ativos para a data
        $bloqueios = BloqueioAgenda::where('profissional_id', $profissionalId)
            ->where('data_inicio', '<=', $dataCarbono->endOfDay())
            ->where('data_fim', '>=', $dataCarbono->startOfDay())
            ->get();

        // Obter agendamentos existentes (não cancelados)
        $agendamentosExistentes = Agendamento::where('profissional_id', $profissionalId)
            ->whereDate('data', $dataCarbono)
            ->whereNotIn('status', [
                AgendamentoStatusEnum::CANCELADO->value,
                AgendamentoStatusEnum::FALTOU->value,
            ])
            ->get();

        $profissional = Profissional::findOrFail($profissionalId);
        $slots = [];

        foreach ($disponibilidades as $disponibilidade) {
            $inicio = Carbon::parse($data . ' ' . $disponibilidade->hora_inicio);
            $fim = Carbon::parse($data . ' ' . $disponibilidade->hora_fim);
            $duracao = $disponibilidade->duracao_consulta ?? $profissional->duracao_consulta;

            $slotInicio = $inicio->copy();
            while ($slotInicio->copy()->addMinutes($duracao)->lte($fim)) {
                $slotFim = $slotInicio->copy()->addMinutes($duracao);
                $slotHoraInicio = $slotInicio->format('H:i');
                $slotHoraFim = $slotFim->format('H:i');

                // Verificar se não está bloqueado
                $bloqueado = $bloqueios->first(function ($bloqueio) use ($slotInicio, $slotFim) {
                    return $slotInicio->lt($bloqueio->data_fim) && $slotFim->gt($bloqueio->data_inicio);
                });

                // Verificar se não há conflito com agendamento existente
                $conflito = $agendamentosExistentes->first(function ($ag) use ($slotHoraInicio, $slotHoraFim) {
                    return $slotHoraInicio < $ag->hora_fim && $slotHoraFim > $ag->hora_inicio;
                });

                $slots[] = [
                    'hora_inicio' => $slotHoraInicio,
                    'hora_fim' => $slotHoraFim,
                    'disponivel' => !$bloqueado && !$conflito,
                ];

                $slotInicio->addMinutes($duracao);
            }
        }

        return $slots;
    }

    /**
     * Valida se o horário não conflita com outro agendamento.
     */
    public function verificarConflito(
        int $profissionalId,
        string $data,
        string $horaInicio,
        string $horaFim,
        ?int $excluirAgendamentoId = null
    ): bool {
        $query = Agendamento::where('profissional_id', $profissionalId)
            ->whereDate('data', $data)
            ->where('hora_inicio', '<', $horaFim)
            ->where('hora_fim', '>', $horaInicio)
            ->whereNotIn('status', [
                AgendamentoStatusEnum::CANCELADO->value,
                AgendamentoStatusEnum::FALTOU->value,
            ]);

        if ($excluirAgendamentoId) {
            $query->where('id', '!=', $excluirAgendamentoId);
        }

        return $query->exists();
    }

    /**
     * Cria um agendamento com validação de conflito.
     */
    public function criarAgendamento(array $data): Agendamento
    {
        $clienteId = Auth::user()->cliente_id;

        // Calcular hora_fim se não informada
        if (empty($data['hora_fim'])) {
            $profissional = Profissional::findOrFail($data['profissional_id']);
            $duracao = $profissional->getDuracaoConsultaParaDia(
                Carbon::parse($data['data'])->dayOfWeek
            );
            $data['hora_fim'] = Carbon::parse($data['hora_inicio'])
                ->addMinutes($duracao)
                ->format('H:i');
        }

        // Verificar conflito
        if ($this->verificarConflito($data['profissional_id'], $data['data'], $data['hora_inicio'], $data['hora_fim'])) {
            throw new \Exception('Já existe um agendamento neste horário.');
        }

        $data['cliente_id'] = $clienteId;
        $data['status'] = $data['status'] ?? AgendamentoStatusEnum::AGENDADO->value;

        return Agendamento::create($data);
    }

    /**
     * Reagenda um agendamento, salvando o histórico.
     */
    public function reagendar(int $agendamentoId, array $novosData): Agendamento
    {
        return DB::transaction(function () use ($agendamentoId, $novosData) {
            $agendamento = Agendamento::findOrFail($agendamentoId);

            // Calcular nova hora_fim se não informada
            if (empty($novosData['hora_fim'])) {
                $profissional = $agendamento->profissional;
                $duracao = $profissional->getDuracaoConsultaParaDia(
                    Carbon::parse($novosData['data'])->dayOfWeek
                );
                $novosData['hora_fim'] = Carbon::parse($novosData['hora_inicio'])
                    ->addMinutes($duracao)
                    ->format('H:i');
            }

            // Verificar conflito no novo horário
            if ($this->verificarConflito(
                $agendamento->profissional_id,
                $novosData['data'],
                $novosData['hora_inicio'],
                $novosData['hora_fim'],
                $agendamentoId
            )) {
                throw new \Exception('Já existe um agendamento neste horário.');
            }

            // Salvar histórico
            HistoricoReagendamento::create([
                'agendamento_id' => $agendamentoId,
                'cliente_id' => $agendamento->cliente_id,
                'data_anterior' => $agendamento->data,
                'hora_anterior' => $agendamento->hora_inicio,
                'nova_data' => $novosData['data'],
                'novo_horario' => $novosData['hora_inicio'],
                'usuario_id' => Auth::id(),
            ]);

            // Atualizar agendamento
            $agendamento->update([
                'data' => $novosData['data'],
                'hora_inicio' => $novosData['hora_inicio'],
                'hora_fim' => $novosData['hora_fim'],
                'status' => AgendamentoStatusEnum::REAGENDADO->value,
            ]);

            return $agendamento->fresh();
        });
    }

    /**
     * Altera o status de um agendamento.
     */
    public function alterarStatus(int $agendamentoId, AgendamentoStatusEnum $novoStatus): Agendamento
    {
        $agendamento = Agendamento::findOrFail($agendamentoId);
        $agendamento->update(['status' => $novoStatus->value]);
        return $agendamento->fresh();
    }

    /**
     * Retorna os agendamentos de um dia para um ou todos os profissionais.
     */
    public function agendamentosDoDia(string $data, ?int $profissionalId = null): Collection
    {
        $clienteId = Auth::user()->cliente_id;

        $query = Agendamento::with(['profissional.user', 'paciente', 'sala'])
            ->where('cliente_id', $clienteId)
            ->whereDate('data', $data);

        $user = Auth::user();
        if (!$user->hasRole('Administrador')) {
            $meuProfissionalId = $user->profissional?->id;
            $idsPermitidos = [];
            if ($meuProfissionalId) {
                $idsPermitidos[] = $meuProfissionalId;
                $acessos = $user->profissional->acessosRecebidos()->pluck('concedente_id')->toArray();
                $idsPermitidos = array_merge($idsPermitidos, $acessos);
            }
            if (empty($idsPermitidos)) {
                $query->whereIn('profissional_id', []);
            } else {
                if ($profissionalId && in_array($profissionalId, $idsPermitidos)) {
                    $query->where('profissional_id', $profissionalId);
                } else {
                    $query->whereIn('profissional_id', $idsPermitidos);
                }
            }
        } else {
            if ($profissionalId) {
                $query->where('profissional_id', $profissionalId);
            }
        }

        return $query->orderBy('hora_inicio')->get();
    }

    /**
     * Retorna a agenda semanal de um profissional.
     */
    public function agendaSemanal(string $dataInicio, ?int $profissionalId = null): array
    {
        $clienteId = Auth::user()->cliente_id;
        $inicio = Carbon::parse($dataInicio)->startOfWeek();
        $fim = $inicio->copy()->endOfWeek();

        $query = Agendamento::with(['profissional.user', 'paciente', 'sala'])
            ->where('cliente_id', $clienteId)
            ->whereBetween('data', [$inicio, $fim]);

        $user = Auth::user();
        if (!$user->hasRole('Administrador')) {
            $meuProfissionalId = $user->profissional?->id;
            $idsPermitidos = [];
            if ($meuProfissionalId) {
                $idsPermitidos[] = $meuProfissionalId;
                $acessos = $user->profissional->acessosRecebidos()->pluck('concedente_id')->toArray();
                $idsPermitidos = array_merge($idsPermitidos, $acessos);
            }
            if (empty($idsPermitidos)) {
                $query->whereIn('profissional_id', []);
            } else {
                if ($profissionalId && in_array($profissionalId, $idsPermitidos)) {
                    $query->where('profissional_id', $profissionalId);
                } else {
                    $query->whereIn('profissional_id', $idsPermitidos);
                }
            }
        } else {
            if ($profissionalId) {
                $query->where('profissional_id', $profissionalId);
            }
        }

        $agendamentos = $query->orderBy('data')->orderBy('hora_inicio')->get();

        // Agrupar por dia
        $dias = [];
        for ($d = $inicio->copy(); $d->lte($fim); $d->addDay()) {
            $dataStr = $d->format('Y-m-d');
            $dias[$dataStr] = [
                'data' => $dataStr,
                'dia_semana' => $d->dayOfWeek,
                'nome_dia' => $d->locale('pt_BR')->isoFormat('ddd'),
                'formatada' => $d->format('d/m'),
                'agendamentos' => $agendamentos->filter(fn($ag) => $ag->data->format('Y-m-d') === $dataStr)->values(),
            ];
        }

        return array_values($dias);
    }

    /**
     * Registrar a hora de chegada do paciente
     */
    public function registrarChegada(int $agendamentoId): void
    {
        $agendamento = Agendamento::where('id', $agendamentoId)
            ->where('cliente_id', auth()->user()->cliente_id)
            ->firstOrFail();

        $agendamento->update([
            'hora_chegada' => now()->format('H:i:s'),
            'status' => 'confirmado' // opcional: mudar status para confirmado se estava apenas agendado
        ]);
    }

    /**
     * Desfazer a hora de chegada do paciente
     */
    public function desfazerChegada(int $agendamentoId): void
    {
        $agendamento = Agendamento::where('id', $agendamentoId)
            ->where('cliente_id', auth()->user()->cliente_id)
            ->firstOrFail();

        $agendamento->update([
            'hora_chegada' => null,
            // não revertemos o status para "agendado" automaticamente, deixamos como confirmado
        ]);
    }
}
