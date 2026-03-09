<?php

namespace App\Services;

use App\Repositories\Contracts\PacienteRepositoryInterface;

class PacienteService
{
    public function __construct(
        protected PacienteRepositoryInterface $pacienteRepository
    ) {}

    public function listar(array $filters = [])
    {
        return $this->pacienteRepository->all($filters);
    }

    public function buscar(int $id)
    {
        return $this->pacienteRepository->find($id);
    }

    public function buscarPorCpf(string $cpf)
    {
        return $this->pacienteRepository->findByCpf($cpf);
    }

    public function pesquisar(string $term)
    {
        return $this->pacienteRepository->search($term);
    }

    public function criar(array $data)
    {
        return $this->pacienteRepository->create($data);
    }

    public function atualizar(int $id, array $data)
    {
        return $this->pacienteRepository->update($id, $data);
    }

    public function excluir(int $id)
    {
        return $this->pacienteRepository->delete($id);
    }
}
