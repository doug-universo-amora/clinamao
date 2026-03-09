<?php

namespace App\Repositories\Contracts;

interface ClienteRepositoryInterface extends BaseRepositoryInterface
{
    public function findBySubdominio(string $subdominio);
}
