<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Óculos de Grau',
                'description' => 'Óculos de grau para diferentes estilos.',
            ],
            [
                'name' => 'Óculos de Sol',
                'description' => 'Óculos de sol para proteção e estilo.',
            ],
            [
                'name' => 'Armações',
                'description' => 'Armações para lentes de grau.',
            ],
            [
                'name' => 'Acessórios',
                'description' => 'Estojos, flanelas e acessórios para óculos.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
