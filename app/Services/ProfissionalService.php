<?php

namespace App\Services;

use App\Repositories\Contracts\ProfissionalRepositoryInterface;

class ProfissionalService
{
    public function __construct(
        protected ProfissionalRepositoryInterface $profissionalRepository
    ) {}

    public function listar(array $filters = [])
    {
        return $this->profissionalRepository->all($filters);
    }

    public function buscar(int $id)
    {
        return $this->profissionalRepository->find($id);
    }

    public function criar(array $data)
    {
        return $this->profissionalRepository->create($data);
    }

    public function atualizar(int $id, array $data)
    {
        return $this->profissionalRepository->update($id, $data);
    }

    public function excluir(int $id)
    {
        return $this->profissionalRepository->delete($id);
    }
}
