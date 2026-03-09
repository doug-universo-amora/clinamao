<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisponibilidadeRequest;
use App\Models\Disponibilidade;
use App\Models\Profissional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class DisponibilidadeController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $clienteId = $user->cliente_id;
        $profissionalId = $request->input('profissional_id');

        $profissionaisQuery = Profissional::where('cliente_id', $clienteId)
            ->where('status', true)
            ->with('user:id,name');

        $idsPermitidos = [];

        if (!$user->hasRole('Administrador')) {
            $meuProfissionalId = $user->profissional?->id;
            if ($meuProfissionalId) {
                // Filtramos APENAS as disponibilidades que este profissional PODE LER
                $idsPermitidos = $user->profissional->getIdsPermitidos('disponibilidade', 'ler');
            }
            if (empty($idsPermitidos)) {
                $profissionaisQuery->whereIn('id', []);
            } else {
                $profissionaisQuery->whereIn('id', $idsPermitidos);
            }
        }

        $profissionais = $profissionaisQuery->get();
        $idsDisponiveis = $profissionais->pluck('id')->toArray();

        // Se não houver profissional selecionado, mas houver apenas 1 disponível, seleciona-o
        if (!$profissionalId && count($idsDisponiveis) === 1) {
            $profissionalId = $idsDisponiveis[0];
        }

        // Se o profissionalId requisitado não estiver na lista permitida, força null
        if ($profissionalId && !in_array($profissionalId, $idsDisponiveis)) {
            $profissionalId = null;
        }

        $disponibilidades = collect();
        if ($profissionalId) {
            $disponibilidades = Disponibilidade::where('profissional_id', $profissionalId)
                ->with('profissional.user')
                ->orderBy('dia_semana')
                ->orderBy('hora_inicio')
                ->get();
        }

        return Inertia::render('Agenda/Disponibilidades/Index', [
            'disponibilidades' => $disponibilidades,
            'profissionais' => $profissionais,
            'profissionalSelecionado' => $profissionalId ? (int) $profissionalId : null,
        ]);
    }

    public function store(DisponibilidadeRequest $request): RedirectResponse
    {
        $this->authorizeAction($request->input('profissional_id'), 'criar');

        $data = $request->validated();
        $data['cliente_id'] = auth()->user()->cliente_id;

        Disponibilidade::create($data);

        return Redirect::route('disponibilidades.index', ['profissional_id' => $data['profissional_id']])
            ->with('success', 'Disponibilidade cadastrada com sucesso.');
    }

    public function update(DisponibilidadeRequest $request, int $id): RedirectResponse
    {
        $disponibilidade = Disponibilidade::findOrFail($id);
        $this->authorizeAction($disponibilidade->profissional_id, 'editar');
        
        $disponibilidade->update($request->validated());

        return Redirect::route('disponibilidades.index', ['profissional_id' => $disponibilidade->profissional_id])
            ->with('success', 'Disponibilidade atualizada com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $disponibilidade = Disponibilidade::findOrFail($id);
        $profissionalId = $disponibilidade->profissional_id;
        
        $this->authorizeAction($profissionalId, 'excluir');
        
        $disponibilidade->delete();

        return Redirect::route('disponibilidades.index', ['profissional_id' => $profissionalId])
            ->with('success', 'Disponibilidade excluída com sucesso.');
    }

    /**
     * Validador/Gatekeeper Interno para Ações de Disponibilidade
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
            $idsPermitidos = $user->profissional->getIdsPermitidos('disponibilidade', $acao);
            if (in_array($alvoProfissionalId, $idsPermitidos)) {
                return;
            }
        }

        abort(403, 'Acesso Negado. Você não tem permissão para ' . $acao . ' disponibilidades nesta agenda.');
    }
}
