<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('cliente_id', auth()->user()->cliente_id)
            ->withCount('users')
            ->paginate(10);

        return Inertia::render('Roles/Index', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0]; // agrupa por prefixo
        });

        return Inertia::render('Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) {
                    return $query->where('cliente_id', auth()->user()->cliente_id);
                })
            ],
            'permissions' => 'array'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'cliente_id' => auth()->user()->cliente_id
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Perfil criado com sucesso.');
    }

    public function edit(Role $role)
    {
        if ((int)$role->cliente_id !== (int)auth()->user()->cliente_id) {
            abort(403);
        }

        if ($role->name === 'Administrador') {
            return redirect()->route('roles.index')->with('error', 'O perfil de Administrador é nativo do sistema e não pode ser editado.');
        }

        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });

        $role->load('permissions');

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $role->permissions->pluck('name')
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ((int)$role->cliente_id !== (int)auth()->user()->cliente_id) {
            abort(403);
        }

        if ($role->name === 'Administrador') {
            return redirect()->route('roles.index')->with('error', 'O perfil de Administrador não pode ser alterado.');
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where(function ($query) {
                    return $query->where('cliente_id', auth()->user()->cliente_id);
                })->ignore($role->id)
            ],
            'permissions' => 'array'
        ]);

        $role->update([
            'name' => $request->name
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')->with('success', 'Perfil atualizado com sucesso.');
    }

    public function destroy(Role $role)
    {
        if ((int)$role->cliente_id !== (int)auth()->user()->cliente_id) {
            abort(403);
        }

        if ($role->name === 'Administrador') {
            return redirect()->back()->with('error', 'O perfil de Administrador principal não pode ser excluído.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Não é possível excluir um perfil que possui usuários vinculados.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Perfil excluído com sucesso.');
    }
}
