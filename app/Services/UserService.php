<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function listar(array $filters = [])
    {
        return $this->userRepository->all($filters);
    }

    public function buscar(int $id)
    {
        return $this->userRepository->find($id);
    }

    public function criar(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = $this->userRepository->create($data);

        // Atribuir role se informada
        if (!empty($data['role'])) {
            setPermissionsTeamId($user->cliente_id);
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function atualizar(int $id, array $data)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $this->userRepository->update($id, $data);

        // Atualizar role se informada
        if (isset($data['role'])) {
            setPermissionsTeamId($user->cliente_id);
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function excluir(int $id)
    {
        return $this->userRepository->delete($id);
    }
}
