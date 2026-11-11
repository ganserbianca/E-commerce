<?php

namespace App\Repositories;

use App\Exceptions\OrderNotFoundException;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{

    public function indexByUser(int $userId): Collection
    {
        return Order::where('user_id', $userId)->with('items')->get();
    }

    public function find(int $id): Order
    {
        return Order::find($id) ?? throw new OrderNotFoundException();
    }

      public function findForUpdate(int $orderId): Order
    {
        return Order::lockForUpdate()->find($orderId)
            ?? throw new OrderNotFoundException();
    }

    public function findByUser(int $userId, int $orderId): Order
    {
        return Order::where('user_id', $userId)->with('items')->find($orderId)
            ?? throw new OrderNotFoundException();
    }

    public function findByUserForUpdate(int $userId, int $orderId): Order
    {
        return Order::where('user_id', $userId)
            ->with('items')
            ->lockForUpdate()
            ->find($orderId)
            ?? throw new OrderNotFoundException();
    }

  
    public function create(array $orderData): Order
    {
        return Order::create($orderData);
    }

    public function update(int $id, array $orderData): Order
    {
        $order = $this->find($id);
        $order->update($orderData);

        return $order->refresh();
    }

    public function loadItems(Order $order): Order
    {
        return $order->load('items');
    }
}