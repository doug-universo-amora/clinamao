<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(): Response
    {
        $clienteId = auth()->user()->cliente_id;
        setPermissionsTeamId($clienteId);

        $usuarios = $this->userService->listar(['cliente_id' => $clienteId])
            ->load('roles');

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
        ]);
    }

    public function create(): Response
    {
        $clienteId = auth()->user()->cliente_id;
        setPermissionsTeamId($clienteId);

        $roles = Role::where('cliente_id', $clienteId)->pluck('name');

        return Inertia::render('Usuarios/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['cliente_id'] = auth()->user()->cliente_id;

        $this->userService->criar($data);

        return Redirect::route('usuarios.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(int $id): Response
    {
        $clienteId = auth()->user()->cliente_id;
        setPermissionsTeamId($clienteId);

        $usuario = $this->userService->buscar($id);
        $usuario->load('roles');

        $roles = Role::where('cliente_id', $clienteId)->pluck('name');

        return Inertia::render('Usuarios/Edit', [
            'usuario' => $usuario,
            'roles' => $roles,
            'currentRole' => $usuario->roles->first()?->name,
        ]);
    }

    public function update(UserUpdateRequest $request, int $id): RedirectResponse
    {
        $this->userService->atualizar($id, $request->validated());

        return Redirect::route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->userService->excluir($id);

        return Redirect::route('usuarios.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
