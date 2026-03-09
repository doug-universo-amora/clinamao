<?php

namespace App\Repositories\Contracts;

use Carbon\Carbon;

interface AgendamentoRepositoryInterface extends BaseRepositoryInterface
{
    public function findByProfissionalAndData(int $profissionalId, Carbon $data);
    public function findByPacienteAndData(int $pacienteId, Carbon $data);
}
