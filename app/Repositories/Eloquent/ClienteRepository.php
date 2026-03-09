<?php

namespace App\Repositories\Eloquent;

use App\Models\Cliente;
use App\Repositories\Contracts\ClienteRepositoryInterface;

class ClienteRepository extends BaseRepository implements ClienteRepositoryInterface
{
    public function __construct(Cliente $model)
    {
        parent::__construct($model);
    }

    public function findBySubdominio(string $subdominio)
    {
        return $this->model->where('subdominio', $subdominio)->firstOrFail();
    }
}
