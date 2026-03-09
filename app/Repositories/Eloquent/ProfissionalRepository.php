<?php

namespace App\Repositories\Eloquent;

use App\Models\Profissional;
use App\Repositories\Contracts\ProfissionalRepositoryInterface;

class ProfissionalRepository extends BaseRepository implements ProfissionalRepositoryInterface
{
    public function __construct(Profissional $model)
    {
        parent::__construct($model);
    }

    public function findByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->first();
    }
}
