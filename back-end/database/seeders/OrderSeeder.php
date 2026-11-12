<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            [
                'email' => 'ana@otica.local',
                'status' => 'pending',
                'product' => 'Óculos de Grau Moderno',
                'quantity' => 1,
            ],
            [
                'email' => 'bruno@otica.local',
                'status' => 'paid',
                'product' => 'Óculos de Sol Quadrado',
                'quantity' => 2,
            ],
            [
                'email' => 'carla@otica.local',
                'status' => 'sent',
                'product' => 'Armação Metálica Prata',
                'quantity' => 1,
            ],
            [
                'email' => 'ana@otica.local',
                'status' => 'delivered',
                'product' => 'Kit de Limpeza para Óculos',
                'quantity' => 2,
            ],
            [
                'email' => 'bruno@otica.local',
                'status' => 'cancelled',
                'product' => 'Óculos de Sol Redondo',
                'quantity' => 1,
            ],
        ];

        DB::transaction(function () use ($orders) {
            foreach ($orders as $data) {
                $this->createDemoOrder($data);
            }
        });
    }

    private function createDemoOrder(array $data): void
    {
        $user = User::where('email', $data['email'])
            ->firstOrFail();

        $address = Address::where('user_id', $user->id)
            ->firstOrFail();

        $product = Product::where('name', $data['product'])
            ->firstOrFail();

        // Identificador estável para evitar pedidos duplicados.
        $transactionCode = 'DEMO-' . strtoupper($data['status'])
            . '-' . $user->id;

        // Um pedido de demonstração é identificado pelo
        // cliente, status inicial e produto escolhido.
        // A consulta aos itens evita depender de IDs fixos.
        $existingOrder = Order::where('user_id', $user->id)
            ->where('status', $data['status'])
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->first();

        if ($existingOrder) {
            return;
        }

        $total = round(
            (float) $product->price * $data['quantity'],
            2
        );

        $isPaid = in_array(
            $data['status'],
            ['paid', 'sent', 'delivered'],
            true
        );

        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => $data['status'],
            'total_amount' => $total,
            'ordered_at' => now(),
            'paid_at' => $isPaid ? now() : null,
            'order_street' => $address->street,
            'order_number' => $address->number,
            'order_cep' => $address->cep,
            'order_neighborhood' => $address->neighborhood,
            'order_city' => $address->city,
            'order_uf' => $address->uf,
            'order_complement' => $address->complement,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => $data['quantity'],
        ]);

        if ($isPaid) {
            Payment::create([
                'order_id' => $order->id,
                'method' => 'pix',
                'status' => 'approved',
                'price' => $total,
                'transaction_code' => $transactionCode,
            ]);
        }
    }
}
