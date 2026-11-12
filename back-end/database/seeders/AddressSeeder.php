<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'email' => 'ana@otica.local',
                'cep' => '85010-000',
                'street' => 'Rua das Flores',
                'number' => '120',
                'complement' => 'Apartamento 201',
                'neighborhood' => 'Centro',
                'city' => 'Guarapuava',
                'uf' => 'PR',
            ],
            [
                'email' => 'bruno@otica.local',
                'cep' => '85015-000',
                'street' => 'Rua das Araucárias',
                'number' => '450',
                'complement' => null,
                'neighborhood' => 'Batel',
                'city' => 'Guarapuava',
                'uf' => 'PR',
            ],
            [
                'email' => 'carla@otica.local',
                'cep' => '85020-000',
                'street' => 'Rua Primavera',
                'number' => '85',
                'complement' => 'Casa',
                'neighborhood' => 'Santa Cruz',
                'city' => 'Guarapuava',
                'uf' => 'PR',
            ],
        ];

        foreach ($addresses as $data) {
            $user = User::where('email', $data['email'])
                ->firstOrFail();

            unset($data['email']);

            Address::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'street' => $data['street'],
                    'number' => $data['number'],
                ],
                $data
            );
        }
    }
}
