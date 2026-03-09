<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Contracts
use App\Repositories\Contracts\ClienteRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ProfissionalRepositoryInterface;
use App\Repositories\Contracts\PacienteRepositoryInterface;
use App\Repositories\Contracts\AgendamentoRepositoryInterface;

// Implementations
use App\Repositories\Eloquent\ClienteRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\ProfissionalRepository;
use App\Repositories\Eloquent\PacienteRepository;
use App\Repositories\Eloquent\AgendamentoRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClienteRepositoryInterface::class, ClienteRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ProfissionalRepositoryInterface::class, ProfissionalRepository::class);
        $this->app->bind(PacienteRepositoryInterface::class, PacienteRepository::class);
        $this->app->bind(AgendamentoRepositoryInterface::class, AgendamentoRepository::class);
    }
}
