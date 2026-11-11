<?php

namespace App\Repositories;

use App\Exceptions\UserNotFoundException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function index(): Collection
    {
        return User::all();
    }

    public function find(int $id): User
    {
        return User::find($id)
            ?? throw new UserNotFoundException();
    }

    public function findByEmail(string $email): ? User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $userData): User
    {
        return User::create($userData);
    }

    public function update(int $id, array $userData): User
    {
        $user = $this->find($id);

        $user->update($userData);

        return $user->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
