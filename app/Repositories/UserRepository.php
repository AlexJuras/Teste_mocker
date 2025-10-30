<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function find(int $id)
    {
        // normalmente buscaria no banco
        return User::find($id);
    }
}
