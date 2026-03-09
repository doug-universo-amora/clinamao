<?php

namespace App\Repositories\Contracts;

interface PacienteRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCpf(string $cpf);
    public function search(string $term);
}
