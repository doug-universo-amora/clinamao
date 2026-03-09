<?php

namespace App\Http\Controllers;

use App\Http\Requests\BloqueioAgendaRequest;
use App\Models\BloqueioAgenda;
use App\Models\Profissional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class BloqueioAgendaController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $clienteId = $user->cliente_id;

        $profissionaisQuery = Profissional::where('cliente_id', $clienteId)
            ->where('status', true)
            ->with('user:id,name');

        $idsPermitidos = [];

        if (!$user->hasRole('Administrador')) {
            $meuProfissionalId = $user->profissional?->id;
            if ($meuProfissionalId) {
                // Filtramos APENAS as agendas que este profissional PODE LER BLOQUEIOS
                $idsPermitidos = $user->profissional->getIdsPermitidos('bloqueio', 'ler');
            }
            if (empty($idsPermitidos)) {
                $profissionaisQuery->whereIn('id', []);
            } else {
                $profissionaisQuery->whereIn('id', $idsPermitidos);
            }
        }

        $profissionais = $profissionaisQuery->get();

        $bloqueiosQuery = BloqueioAgenda::where('cliente_id', $clienteId)
            ->with('profissional.user')
            ->orderBy('data_inicio', 'desc');

        if (!$user->hasRole('Administrador')) {
            $idsPossiveis = empty($idsPermitidos) ? [0] : $idsPermitidos;
            $bloqueiosQuery->whereIn('profissional_id', $idsPossiveis);
        }

        $bloqueios = $bloqueiosQuery->get();

        return Inertia::render('Agenda/Bloqueios/Index', [
            'bloqueios' => $bloqueios,
            'profissionais' => $profissionais,
        ]);
    }

    public function store(BloqueioAgendaRequest $request): RedirectResponse
    {
        $this->authorizeAction($request->input('profissional_id'), 'criar');

        $data = $request->validated();
        $data['cliente_id'] = auth()->user()->cliente_id;

        BloqueioAgenda::create($data);

        return Redirect::route('bloqueios.index')
            ->with('success', 'Bloqueio cadastrado com sucesso.');
    }

    public function update(BloqueioAgendaRequest $request, int $id): RedirectResponse
    {
        $bloqueio = BloqueioAgenda::findOrFail($id);
        $this->authorizeAction($bloqueio->profissional_id, 'editar');
        
        $bloqueio->update($request->validated());

        return Redirect::route('bloqueios.index')
            ->with('success', 'Bloqueio atualizado com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $bloqueio = BloqueioAgenda::findOrFail($id);
        $this->authorizeAction($bloqueio->profissional_id, 'excluir');
        
        $bloqueio->delete();

        return Redirect::route('bloqueios.index')
            ->with('success', 'Bloqueio excluído com sucesso.');
    }

    /**
     * Validador/Gatekeeper Interno para Ações de Bloqueio
     */
    private function authorizeAction(int $alvoProfissionalId, string $acao): void
    {
        $user = auth()->user();
        if ($user->hasRole('Administrador')) {
            return;
        }

        $meuProfissionalId = $user->profissional?->id;
        
        if ($meuProfissionalId === $alvoProfissionalId) {
            return;
        }

        if ($meuProfissionalId) {
            $idsPermitidos = $user->profissional->getIdsPermitidos('bloqueio', $acao);
            if (in_array($alvoProfissionalId, $idsPermitidos)) {
                return;
            }
        }

        abort(403, 'Acesso Negado. Você não tem permissão para ' . $acao . ' bloqueios nesta agenda.');
    }
}
