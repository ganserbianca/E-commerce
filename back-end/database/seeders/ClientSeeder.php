<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Ana Oliveira',
                'email' => 'ana@otica.local',
                'password' => 'Cliente123!',
            ],
            [
                'name' => 'Bruno Santos',
                'email' => 'bruno@otica.local',
                'password' => 'Cliente123!',
            ],
            [
                'name' => 'Carla Ferreira',
                'email' => 'carla@otica.local',
                'password' => 'Cliente123!',
            ],
        ];

        foreach ($clients as $client) {
            User::updateOrCreate(
                ['email' => $client['email']],
                [
                    'name' => $client['name'],
                    'password' => $client['password'],
                    'type_user' => 'customer',
                ]
            );
        }
    }
}
