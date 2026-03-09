<?php

namespace App\Observers;

use App\Models\Cliente;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ClienteObserver
{
    /**
     * Ao criar um cliente, gerar automaticamente as roles e permissões padrão.
     */
    public function created(Cliente $cliente): void
    {
        // Definir o contexto de team para este cliente
        setPermissionsTeamId($cliente->id);

        // ─── Permissões do Sistema ──────────────────────────────
        $permissoes = [
            // Usuários
            'usuarios.listar',
            'usuarios.criar',
            'usuarios.editar',
            'usuarios.excluir',

            // Roles
            'roles.listar',
            'roles.criar',
            'roles.editar',
            'roles.excluir',

            // Pacientes
            'pacientes.listar',
            'pacientes.criar',
            'pacientes.editar',
            'pacientes.excluir',

            // Profissionais
            'profissionais.listar',
            'profissionais.criar',
            'profissionais.editar',
            'profissionais.excluir',

            // Agenda
            'agenda.visualizar',
            'agenda.criar',
            'agenda.editar',
            'agenda.cancelar',

            // Agendamentos
            'agendamentos.criar',
            'agendamentos.editar',
            'agendamentos.cancelar',
            'agendamentos.reagendar',

            // Relatórios
            'relatorios.visualizar',

            // Configurações
            'configuracoes.editar',

            // Módulos
            'modulos.gerenciar',

            // Gestão de Perfis de Acesso
            'roles.listar',
            'roles.criar',
            'roles.editar',
            'roles.excluir',
        ];

        // Criar todas as permissões
        foreach ($permissoes as $permissao) {
            Permission::findOrCreate($permissao, 'web');
        }

        // ─── Roles Padrão ───────────────────────────────────────

        // 1. Administrador — acesso total
        $admin = Role::findOrCreate('Administrador', 'web');
        $admin->givePermissionTo($permissoes);

        // 2. Profissional de Saúde
        $profissional = Role::findOrCreate('Profissional de Saúde', 'web');
        $profissional->givePermissionTo([
            'agenda.visualizar',
            'agenda.criar',
            'agenda.editar',
            'agenda.cancelar',
            'agendamentos.criar',
            'agendamentos.editar',
            'agendamentos.cancelar',
            'pacientes.listar',
        ]);

        // 3. Recepção
        $recepcao = Role::findOrCreate('Recepção', 'web');
        $recepcao->givePermissionTo([
            'agenda.visualizar',
            'agenda.criar',
            'agenda.editar',
            'agenda.cancelar',
            'agendamentos.criar',
            'agendamentos.editar',
            'agendamentos.cancelar',
            'pacientes.listar',
            'pacientes.criar',
            'pacientes.editar',
        ]);

        // 4. Secretaria
        $secretaria = Role::findOrCreate('Secretaria', 'web');
        $secretaria->givePermissionTo([
            'agenda.visualizar',
            'agenda.criar',
            'agenda.editar',
            'agenda.cancelar',
            'agendamentos.criar',
            'agendamentos.editar',
            'agendamentos.cancelar',
            'agendamentos.reagendar',
            'pacientes.listar',
            'pacientes.criar',
            'pacientes.editar',
            'relatorios.visualizar',
        ]);

        // 5. Gestão
        $gestao = Role::findOrCreate('Gestão', 'web');
        $gestao->givePermissionTo([
            'agenda.visualizar',
            'pacientes.listar',
            'relatorios.visualizar',
        ]);
    }
}
