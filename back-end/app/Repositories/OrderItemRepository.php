<?php

namespace App\Repositories;

use App\Exceptions\OrderItemNotFoundException;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;


class OrderItemRepository
{
    public function index(): Collection
    {
        return OrderItem::all();
    }

    public function find(int $id): OrderItem
    {
        return OrderItem::find($id) ?? throw new OrderItemNotFoundException();
    }

    public function create(array $orderItemData): OrderItem
    {
        return OrderItem::create($orderItemData);
    }

    public function update(int $id, array $orderItemData): OrderItem
    {
        $orderItem = $this->find($id);
        $orderItem->update($orderItemData);

        return $orderItem->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
