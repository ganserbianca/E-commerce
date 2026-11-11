<?php

namespace App\Services;

use App\Exceptions\InvalidCurrentPasswordException;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function show(int $id): User
    {
        return $this->userRepository->find($id);
    }

    public function update(int $id, array $userData): User
    {
        return $this->userRepository->update($id, $userData);
    }

    public function updatePassword(
        int $id,
        string $currentPassword,
        string $newPassword
    ): User {
        $user = $this->userRepository->find($id);

        if (!Hash::check($currentPassword, $user->password)) {
            throw new InvalidCurrentPasswordException();
        }

        return $this->userRepository->update($id, [
            'password' => Hash::make($newPassword),
        ]);
    }
}