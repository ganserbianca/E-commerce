<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Carrinho de visitante
        $visitorCart = Cart::updateOrCreate(
            ['token' => 'visitante-demo-lume'],
            ['user_id' => null]
        );

        $this->addItem(
            $visitorCart,
            'Óculos de Sol Aviador',
            2
        );

        $this->addItem(
            $visitorCart,
            'Estojo Rígido Preto',
            1
        );

        // Carrinho de cliente autenticado
        $customer = User::where(
            'email',
            'ana@otica.local'
        )->firstOrFail();

        $customerCart = Cart::updateOrCreate(
            ['user_id' => $customer->id],
            ['token' => null]
        );

        $this->addItem(
            $customerCart,
            'Óculos de Grau Clássico',
            1
        );

        $this->addItem(
            $customerCart,
            'Flanela de Microfibra',
            2
        );
    }

    private function addItem(
        Cart $cart,
        string $productName,
        int $quantity
    ): void {
        $product = Product::where(
            'name',
            $productName
        )->firstOrFail();

        CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_id' => $product->id,
            ],
            [
                'quantity' => $quantity,
            ]
        );
    }
}
