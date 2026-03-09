<?php

namespace App\Repositories\Eloquent;

use App\Models\Agendamento;
use App\Repositories\Contracts\AgendamentoRepositoryInterface;
use Carbon\Carbon;

class AgendamentoRepository extends BaseRepository implements AgendamentoRepositoryInterface
{
    public function __construct(Agendamento $model)
    {
        parent::__construct($model);
    }

    public function findByProfissionalAndData(int $profissionalId, Carbon $data)
    {
        return $this->model
            ->where('profissional_id', $profissionalId)
            ->whereDate('data', $data)
            ->orderBy('hora_inicio')
            ->get();
    }

    public function findByPacienteAndData(int $pacienteId, Carbon $data)
    {
        return $this->model
            ->where('paciente_id', $pacienteId)
            ->whereDate('data', $data)
            ->orderBy('hora_inicio')
            ->get();
    }
}
