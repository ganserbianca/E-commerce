<?php

namespace Database\Seeders;

use App\Models\Audience;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::where('name', 'Ótica própria')->firstOrFail();

        $categories = Category::whereIn('name', [
            'Óculos de Grau',
            'Óculos de Sol',
            'Armações',
            'Acessórios',
        ])->pluck('id', 'name');

        $audiences = Audience::whereIn('name', [
            'Adulto',
            'Infantil',
        ])->pluck('id', 'name');

        $products = [
            // Óculos de Grau - 10 produtos
            ['Óculos de Grau', 'Óculos de Grau Clássico', 189.90, 15, true, 'Adulto'],
            ['Óculos de Grau', 'Óculos de Grau Moderno', 229.90, 12, true, 'Adulto'],
            ['Óculos de Grau', 'Óculos de Grau Elegance', 279.90, 8, true, 'Adulto'],
            ['Óculos de Grau', 'Óculos de Grau Slim', 159.90, 20, true, 'Adulto'],
            ['Óculos de Grau', 'Óculos de Grau Premium', 399.90, 5, true, 'Adulto'],
            ['Óculos de Grau', 'Óculos de Grau Infantil Azul', 129.90, 14, true, 'Infantil'],
            ['Óculos de Grau', 'Óculos de Grau Infantil Rosa', 129.90, 11, true, 'Infantil'],
            ['Óculos de Grau', 'Óculos de Grau Flex', 219.90, 9, true, 'Adulto'],
            ['Óculos de Grau', 'Óculos de Grau Urban', 249.90, 7, true, 'Adulto'],
            ['Óculos de Grau', 'Óculos de Grau Vintage', 299.90, 6, true, 'Adulto'],

            // Óculos de Sol - 10 produtos
            ['Óculos de Sol', 'Óculos de Sol Aviador', 199.90, 18, true, 'Adulto'],
            ['Óculos de Sol', 'Óculos de Sol Quadrado', 179.90, 12, true, 'Adulto'],
            ['Óculos de Sol', 'Óculos de Sol Redondo', 169.90, 10, true, 'Adulto'],
            ['Óculos de Sol', 'Óculos de Sol Esportivo', 249.90, 8, true, 'Adulto'],
            ['Óculos de Sol', 'Óculos de Sol Polarizado', 329.90, 5, true, 'Adulto'],
            ['Óculos de Sol', 'Óculos de Sol Infantil', 99.90, 16, true, 'Infantil'],
            ['Óculos de Sol', 'Óculos de Sol Retrô', 219.90, 7, true, 'Adulto'],
            ['Óculos de Sol', 'Óculos de Sol Fashion', 259.90, 9, true, 'Adulto'],
            ['Óculos de Sol', 'Óculos de Sol Sunset', 189.90, 0, true, 'Adulto'],
            ['Óculos de Sol', 'Óculos de Sol Exclusive', 449.90, 4, false, 'Adulto'],

            // Armações - 5 produtos
            ['Armações', 'Armação Metálica Prata', 149.90, 20, true, 'Adulto'],
            ['Armações', 'Armação Metálica Dourada', 169.90, 14, true, 'Adulto'],
            ['Armações', 'Armação Acetato Preto', 189.90, 11, true, 'Adulto'],
            ['Armações', 'Armação Transparente', 159.90, 13, true, 'Adulto'],
            ['Armações', 'Armação Infantil Flexível', 119.90, 17, true, 'Infantil'],

            // Acessórios - 5 produtos
            ['Acessórios', 'Estojo Rígido Preto', 39.90, 35, true, 'Adulto'],
            ['Acessórios', 'Estojo Colorido Infantil', 29.90, 25, true, 'Infantil'],
            ['Acessórios', 'Flanela de Microfibra', 14.90, 60, true, 'Adulto'],
            ['Acessórios', 'Cordão para Óculos', 24.90, 40, true, 'Adulto'],
            ['Acessórios', 'Kit de Limpeza para Óculos', 49.90, 30, true, 'Adulto'],
        ];

        foreach ($products as [
            $categoryName,
            $name,
            $price,
            $stock,
            $available,
            $audienceName
        ]) {
            $product = Product::updateOrCreate(
                ['name' => $name],
                [
                    'category_id' => $categories[$categoryName],
                    'brand_id' => $brand->id,
                    'description' => 'Produto de demonstração da Lume Ótica: ' . $name,
                    'price' => $price,
                    'stock_quantity' => $stock,
                    'available' => $available,
                ]
            );

            $product->audiences()->sync([
                $audiences[$audienceName]
            ]);
        }
    }
}
