<?php

namespace App\Repositories\Contracts;

interface ProfissionalRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUserId(int $userId);
}
