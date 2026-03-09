<?php

namespace App\Repositories\Eloquent;

use App\Models\Paciente;
use App\Repositories\Contracts\PacienteRepositoryInterface;

class PacienteRepository extends BaseRepository implements PacienteRepositoryInterface
{
    public function __construct(Paciente $model)
    {
        parent::__construct($model);
    }

    public function findByCpf(string $cpf)
    {
        return $this->model->where('cpf', $cpf)->first();
    }

    public function search(string $term)
    {
        return $this->model->where(function ($query) use ($term) {
            $query->where('nome', 'like', "%{$term}%")
                  ->orWhere('cpf', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
        })->get();
    }
}
