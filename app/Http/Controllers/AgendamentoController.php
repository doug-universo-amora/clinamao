<?php

namespace App\Http\Controllers;

use App\Enums\AgendamentoStatusEnum;
use App\Http\Requests\AgendamentoReagendarRequest;
use App\Http\Requests\AgendamentoStoreRequest;
use App\Models\Paciente;
use App\Models\Profissional;
use App\Models\Sala;
use App\Services\AgendaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class AgendamentoController extends Controller
{
    public function __construct(
        protected AgendaService $agendaService
    ) {}

    public function index(Request $request): Response
    {
        $clienteId = auth()->user()->cliente_id;
        $data = $request->input('data', now()->format('Y-m-d'));
        $profissionalId = $request->input('profissional_id');
        $visualizacao = $request->input('visualizacao', 'dia');

        $profissionais = Profissional::where('cliente_id', $clienteId)
            ->where('status', true)
            ->with('user:id,name')
            ->get();

        $agenda = [];
        if ($visualizacao === 'semana') {
            $agenda = $this->agendaService->agendaSemanal($data, $profissionalId);
        } else {
            $agendamentos = $this->agendaService->agendamentosDoDia($data, $profissionalId);
            $agenda = $agendamentos;
        }

        return Inertia::render('Agenda/Index', [
            'agenda' => $agenda,
            'profissionais' => $profissionais,
            'filtros' => [
                'data' => $data,
                'profissional_id' => $profissionalId ? (int) $profissionalId : null,
                'visualizacao' => $visualizacao,
            ],
            'statusOptions' => array_map(fn($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'cor' => $case->cor(),
            ], AgendamentoStatusEnum::cases()),
        ]);
    }

    public function create(Request $request): Response
    {
        $clienteId = auth()->user()->cliente_id;

        $profissionais = Profissional::where('cliente_id', $clienteId)
            ->where('status', true)
            ->with('user:id,name')
            ->get();

        $pacientes = Paciente::where('cliente_id', $clienteId)
            ->where('status', 'ativo')
            ->orderBy('nome')
            ->select('id', 'nome', 'cpf')
            ->get();

        $salas = Sala::where('cliente_id', $clienteId)
            ->where('status', true)
            ->select('id', 'nome')
            ->get();

        return Inertia::render('Agenda/Agendamentos/Create', [
            'profissionais' => $profissionais,
            'pacientes' => $pacientes,
            'salas' => $salas,
            'dataInicial' => $request->input('data', now()->format('Y-m-d')),
            'profissionalInicial' => $request->input('profissional_id'),
            'horaInicial' => $request->input('hora_inicio'),
        ]);
    }

    public function store(AgendamentoStoreRequest $request): RedirectResponse
    {
        try {
            $this->agendaService->criarAgendamento($request->validated());

            return Redirect::route('agenda.index', ['data' => $request->input('data')])
                ->with('success', 'Agendamento criado com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()
                ->withErrors(['hora_inicio' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * API: Retorna slots disponíveis para um profissional em uma data.
     */
    public function horariosDisponiveis(Request $request): JsonResponse
    {
        $request->validate([
            'profissional_id' => 'required|integer',
            'data' => 'required|date',
        ]);

        $slots = $this->agendaService->gerarHorariosDisponiveis(
            $request->input('profissional_id'),
            $request->input('data')
        );

        return response()->json($slots);
    }

    /**
     * Reagendar um agendamento.
     */
    public function reagendar(AgendamentoReagendarRequest $request, int $id): RedirectResponse
    {
        try {
            $this->agendaService->reagendar($id, $request->validated());

            return Redirect::route('agenda.index')
                ->with('success', 'Agendamento reagendado com sucesso.');
        } catch (\Exception $e) {
            return Redirect::back()
                ->withErrors(['data' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Alterar status de um agendamento.
     */
    public function alterarStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate(['status' => 'required|string']);

        $novoStatus = AgendamentoStatusEnum::from($request->input('status'));
        $this->agendaService->alterarStatus($id, $novoStatus);

        return Redirect::back()
            ->with('success', 'Status atualizado com sucesso.');
    }

    /**
     * Cancelar agendamento.
     */
    public function cancelar(int $id): RedirectResponse
    {
        $this->agendaService->alterarStatus($id, AgendamentoStatusEnum::CANCELADO);

        return Redirect::back()
            ->with('success', 'Agendamento cancelado com sucesso.');
    }

    /**
     * Registrar a chegada do paciente (hora_chegada).
     */
    public function registrarChegada(int $id): RedirectResponse
    {
        $this->agendaService->registrarChegada($id);
        
        return Redirect::back()
            ->with('success', 'Chegada do paciente registrada!');
    }

    /**
     * Desfazer a chegada do paciente (limpar hora_chegada).
     */
    public function desfazerChegada(int $id): RedirectResponse
    {
        $this->agendaService->desfazerChegada($id);
        
        return Redirect::back()
            ->with('success', 'Chegada do paciente desmarcada!');
    }
}
