<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getUserName(int $id): string
    {
        $user = $this->repository->find($id);
        return $user->name ?? 'Usuário não encontrado';
    }

    public function createUser(array $data)
    {
        // Validação básica
        if (empty($data['name']) || empty($data['email'])) {
            throw new \InvalidArgumentException('Nome e email são obrigatórios');
        }

        return $this->repository->create($data);
    }

    public function updateUser(int $id, array $data)
    {
        $user = $this->repository->find($id);
        
        if (!$user) {
            return null;
        }

        return $this->repository->update($id, $data);
    }

    public function deleteUser(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getAllUsers()
    {
        return $this->repository->all();
    }
}
