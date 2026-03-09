<?php

namespace App\Http\Controllers;

use App\Http\Requests\PacienteStoreRequest;
use App\Http\Requests\PacienteUpdateRequest;
use App\Enums\PacienteStatusEnum;
use App\Services\PacienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class PacienteController extends Controller
{
    public function __construct(
        protected PacienteService $pacienteService
    ) {}

    public function index(Request $request): Response
    {
        $clienteId = auth()->user()->cliente_id;

        $query = \App\Models\Paciente::where('cliente_id', $clienteId);

        // Busca por nome, CPF ou email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pacientes = $query->orderBy('nome')->paginate(15)->withQueryString();

        return Inertia::render('Pacientes/Index', [
            'pacientes' => $pacientes,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        $statusOptions = array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], PacienteStatusEnum::cases());

        return Inertia::render('Pacientes/Create', [
            'statusOptions' => $statusOptions,
        ]);
    }

    public function store(PacienteStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['cliente_id'] = auth()->user()->cliente_id;

        // Remove senha vazia (não definir acesso ao portal), senão marca como provisória
        if (empty($data['senha'])) {
            unset($data['senha']);
        } else {
            $data['senha_provisoria'] = true;
        }

        $this->pacienteService->criar($data);

        return Redirect::route('pacientes.index')
            ->with('success', 'Paciente cadastrado com sucesso.');
    }

    public function show(int $id): Response
    {
        $paciente = $this->pacienteService->buscar($id);
        $paciente->load('agendamentos.profissional.user');

        return Inertia::render('Pacientes/Show', [
            'paciente' => $paciente,
        ]);
    }

    public function edit(int $id): Response
    {
        $paciente = $this->pacienteService->buscar($id);

        $statusOptions = array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], PacienteStatusEnum::cases());

        // Indicar se já possui senha (sem expor a senha em si)
        $pacienteData = $paciente->toArray();
        $pacienteData['tem_senha'] = !empty($paciente->senha);

        return Inertia::render('Pacientes/Edit', [
            'paciente' => $pacienteData,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(PacienteUpdateRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();

        // Se senha veio vazia, não alterar a senha atual. Se veio preenchida, é nova provisória.
        if (empty($data['senha'])) {
            unset($data['senha']);
        } else {
            $data['senha_provisoria'] = true;
        }

        $this->pacienteService->atualizar($id, $data);

        return Redirect::route('pacientes.index')
            ->with('success', 'Paciente atualizado com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->pacienteService->excluir($id);

        return Redirect::route('pacientes.index')
            ->with('success', 'Paciente excluído com sucesso.');
    }
}
