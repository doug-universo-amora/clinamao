<?php

namespace App\Models\Traits;

use App\Helpers\TenantHelper;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $clienteId = TenantHelper::getClienteId();
            if ($clienteId) {
                $builder->where($builder->getModel()->getTable() . '.cliente_id', $clienteId);
            }
        });

        static::creating(function ($model) {
            if (!$model->cliente_id) {
                $model->cliente_id = TenantHelper::getClienteId();
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id');
    }
}
