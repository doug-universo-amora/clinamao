<?php

namespace App\Services;

use App\Repositories\Contracts\ClienteRepositoryInterface;

class ClienteService
{
    public function __construct(
        protected ClienteRepositoryInterface $clienteRepository
    ) {}

    public function listar(array $filters = [])
    {
        return $this->clienteRepository->all($filters);
    }

    public function buscar(int $id)
    {
        return $this->clienteRepository->find($id);
    }

    public function buscarPorSubdominio(string $subdominio)
    {
        return $this->clienteRepository->findBySubdominio($subdominio);
    }

    public function criar(array $data)
    {
        return $this->clienteRepository->create($data);
    }

    public function atualizar(int $id, array $data)
    {
        return $this->clienteRepository->update($id, $data);
    }

    public function excluir(int $id)
    {
        return $this->clienteRepository->delete($id);
    }
}
