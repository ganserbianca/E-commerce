<?php

namespace App\Services;

use App\Exceptions\EmptyCartException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidOrderStatusException;
use App\Exceptions\OrderCannotBeCancelledException;
use App\Exceptions\OrderNotPendingException;
use App\Exceptions\ProductUnavailableException;
use App\Models\Address;
use App\Models\Order;
use App\Repositories\AddressRepository;
use App\Repositories\CartRepository;
use App\Repositories\OrderItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderItemRepository $orderItemRepository,
        private readonly AddressRepository $addressRepository,
        private readonly CartRepository $cartRepository,
        private readonly ProductRepository $productRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly TransactionRepository $transaction,
    ) {}

    public function index(int $userId): Collection
    {
        return $this->orderRepository->indexByUser($userId);
    }

    public function show(int $userId, int $orderId): Order
    {
        return $this->orderRepository->findByUser($userId, $orderId);
    }

    public function checkout(int $userId, int $addressId): Order
    {
        return $this->transaction->run(function () use ($userId, $addressId) {
            $address = $this->addressRepository->findByUser($userId, $addressId);
            $cart = $this->cartRepository->findByUserWithItems($userId);

            if (!$cart || $cart->items->isEmpty()) {
                throw new EmptyCartException();
            }

            $totalAmount = 0.0;
            $lines = [];

            foreach ($cart->items as $item) {
                $product = $this->productRepository->findForUpdate($item->product_id);

                if (!$product->available) {
                    throw new ProductUnavailableException();
                }

                if ($item->quantity > $product->stock_quantity) {
                    throw new InsufficientStockException($product->stock_quantity);
                }

                $totalAmount += (float) $product->price * $item->quantity;
                $lines[] = [
                    'product' => $product,
                    'quantity' => $item->quantity,
                ];
            }

            $order = $this->orderRepository->create(
                $this->buildOrderData($userId, $address, $totalAmount)
            );

            foreach ($lines as $line) {
                $product = $line['product'];

                $this->orderItemRepository->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $line['quantity'],
                ]);

                $this->productRepository->decrementStock(
                    $product,
                    $line['quantity']
                );
            }

            $this->cartRepository->clearItems($cart);

            return $this->orderRepository->loadItems($order);
        });
    }

    public function cancel(int $userId, int $orderId): Order
    {
        return $this->transaction->run(function () use ($userId, $orderId) {
            $order = $this->orderRepository->findByUserForUpdate($userId, $orderId);

            if ($order->status !== 'pending') {
                throw new OrderCannotBeCancelledException();
            }

            foreach ($order->items as $item) {
                $product = $this->productRepository->findForUpdate(
                    $item->product_id
                );

                $this->productRepository->incrementStock(
                    $product,
                    $item->quantity
                );
            }

            $order = $this->orderRepository->update($order->id, [
                'status' => 'cancelled',
            ]);

            return $this->orderRepository->loadItems($order);
        });
    }

    public function pay(
        int $userId,
        int $orderId,
        string $method
    ): Order {
        return $this->transaction->run(function () use (
            $userId,
            $orderId,
            $method
        ) {
            $order = $this->orderRepository->findByUserForUpdate(
                $userId,
                $orderId
            );

            if ($order->status !== 'pending') {
                throw new OrderNotPendingException();
            }

            $this->paymentRepository->create([
                'order_id' => $order->id,
                'method' => $method,
                'status' => 'approved',
                'price' => $order->total_amount,
                'transaction_code' => 'SIM-' . uniqid(),
            ]);

            $order = $this->orderRepository->update($order->id, [
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            return $this->orderRepository->loadItems($order);
        });
    }

    public function updateStatus(int $orderId, string $status): Order
    {
        return $this->transaction->run(function () use ($orderId, $status) {
            $order = $this->orderRepository->findForUpdate($orderId);

            $transitions = [
                'paid' => 'sent',
                'sent' => 'delivered',
            ];

            if (($transitions[$order->status] ?? null) !== $status) {
                throw new InvalidOrderStatusException();
            }

            return $this->orderRepository->update($order->id, [
                'status' => $status,
            ]);
        });
    }

    private function buildOrderData(
        int $userId,
        Address $address,
        float $totalAmount
    ): array {
        return [
            'user_id' => $userId,
            'address_id' => $address->id,
            'status' => 'pending',
            'total_amount' => $totalAmount,
            'ordered_at' => now(),
            'order_street' => $address->street,
            'order_number' => $address->number,
            'order_cep' => $address->cep,
            'order_neighborhood' => $address->neighborhood,
            'order_city' => $address->city,
            'order_uf' => $address->uf,
            'order_complement' => $address->complement,
        ];
    }
}
