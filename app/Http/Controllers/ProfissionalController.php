<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfissionalStoreRequest;
use App\Http\Requests\ProfissionalUpdateRequest;
use App\Models\User;
use App\Services\ProfissionalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfissionalController extends Controller
{
    public function __construct(
        protected ProfissionalService $profissionalService
    ) {}

    public function index(): Response
    {
        $clienteId = auth()->user()->cliente_id;

        $profissionais = $this->profissionalService->listar(['cliente_id' => $clienteId])
            ->load('user');

        return Inertia::render('Profissionais/Index', [
            'profissionais' => $profissionais,
        ]);
    }

    public function create(): Response
    {
        $clienteId = auth()->user()->cliente_id;

        // Listar usuários da clínica que não são profissionais ainda
        $usuarios = User::where('cliente_id', $clienteId)
            ->whereDoesntHave('profissional')
            ->select('id', 'name', 'email')
            ->get();

        return Inertia::render('Profissionais/Create', [
            'usuarios' => $usuarios,
        ]);
    }

    public function store(ProfissionalStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['cliente_id'] = auth()->user()->cliente_id;

        $this->profissionalService->criar($data);

        return Redirect::route('profissionais.index')
            ->with('success', 'Profissional criado com sucesso.');
    }

    public function edit(int $id): Response
    {
        $profissional = $this->profissionalService->buscar($id);
        $profissional->load('user');

        return Inertia::render('Profissionais/Edit', [
            'profissional' => $profissional,
        ]);
    }

    public function update(ProfissionalUpdateRequest $request, int $id): RedirectResponse
    {
        $this->profissionalService->atualizar($id, $request->validated());

        return Redirect::route('profissionais.index')
            ->with('success', 'Profissional atualizado com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->profissionalService->excluir($id);

        return Redirect::route('profissionais.index')
            ->with('success', 'Profissional excluído com sucesso.');
    }
}
