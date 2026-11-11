<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (!$email || !$password) {
            throw new RuntimeException(
                'Defina ADMIN_EMAIL e ADMIN_PASSWORD no .env'
            );
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrador',
                'password' => $password,
                'type_user' => 'admin',
            ]
        );
    }
}