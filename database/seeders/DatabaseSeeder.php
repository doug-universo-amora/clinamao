<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Paciente;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuloSeeder::class,
        ]);

        // Criar clínica demo (o Observer cria roles/permissões automaticamente)
        $cliente = Cliente::create([
            'nome' => 'Clínica Demo',
            'subdominio' => 'demo',
            'cnpj' => '00.000.000/0001-00',
            'email' => 'admin@clinicademo.com.br',
            'telefone' => '(11) 99999-0000',
            'status' => true,
        ]);

        // Criar usuário administrador
        $admin = \App\Models\User::create([
            'cliente_id' => $cliente->id,
            'name' => 'Administrador',
            'cpf' => '000.000.000-00',
            'email' => 'admin@clinicademo.com.br',
            'telefone' => '(11) 99999-0000',
            'password' => bcrypt('password'),
            'status' => true,
        ]);

        // Atribuir role de Administrador
        setPermissionsTeamId($cliente->id);
        $admin->assignRole('Administrador');

        // Criar paciente demo (para testar o Portal do Paciente)
        Paciente::create([
            'cliente_id' => $cliente->id,
            'nome' => 'Paciente Demo',
            'cpf' => '111.111.111-11',
            'telefone' => '(11) 98888-0000',
            'email' => 'paciente@demo.com.br',
            'data_nascimento' => '1990-01-15',
            'status' => 'ativo',
            'senha' => bcrypt('password'),
        ]);
    }
}
