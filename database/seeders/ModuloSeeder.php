<?php

namespace Database\Seeders;

use App\Models\Modulo;
use Illuminate\Database\Seeder;

class ModuloSeeder extends Seeder
{
    public function run(): void
    {
        Modulo::create([
            'nome' => 'Agenda',
            'slug' => 'agenda',
            'descricao' => 'Módulo de gestão de agenda, disponibilidades, bloqueios e agendamentos.',
        ]);
    }
}
