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
}
