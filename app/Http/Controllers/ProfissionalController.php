<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfissionalStoreRequest;
use App\Http\Requests\ProfissionalUpdateRequest;
use App\Models\User;
use App\Services\ProfissionalService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Profissional;

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

    public function acessos(int $id): Response
    {
        $profissional = $this->profissionalService->buscar($id);
        $profissional->load('user');
        
        $clienteId = auth()->user()->cliente_id;
        
        $outrosProfissionais = Profissional::where('cliente_id', $clienteId)
            ->where('id', '!=', $id)
            ->with('user:id,name')
            ->get();
            
        // Mapa: concedente_id => permissoes (array/object)
        $acessosRecebidos = $profissional->acessosRecebidos->keyBy('id')->map(function ($acesso) {
            $permissoes = $acesso->pivot->permissoes;
            return is_string($permissoes) ? json_decode($permissoes, true) : ($permissoes ?? []);
        })->toArray();

        return Inertia::render('Profissionais/Acessos', [
            'profissional' => $profissional,
            'outrosProfissionais' => $outrosProfissionais,
            'acessosRecebidos' => (object) $acessosRecebidos,
        ]);
    }

    public function syncAcessos(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'acessos' => 'array'
        ]);

        $profissional = Profissional::findOrFail($id);
        $clienteId = auth()->user()->cliente_id;
        
        $syncData = [];
        foreach ($request->input('acessos', []) as $concedenteId => $permissoes) {
            // Ignora se não enviou permissões (não foi marcado nenhuma opção)
            // Mas o front-end pode mandar um objeto com chaves falsas. 
            // Vamos apenas salvar o objeto json.
            $syncData[$concedenteId] = [
                'cliente_id' => $clienteId, 
                'permissoes' => is_array($permissoes) ? json_encode($permissoes) : $permissoes
            ];
        }

        $profissional->acessosRecebidos()->sync($syncData);

        return Redirect::route('profissionais.index')
            ->with('success', 'Acessos atualizados com sucesso.');
    }
}
