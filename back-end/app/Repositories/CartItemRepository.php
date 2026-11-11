<?php

namespace App\Repositories;

use App\Exceptions\CartItemNotFoundException;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

class CartItemRepository
{
    public function index(int $cartId): Collection
    {
        return CartItem::where('cart_id', $cartId)->get();
    }

    public function find(int $id): CartItem
    {
        return CartItem::find($id)
            ?? throw new CartItemNotFoundException();
    }

    public function findByCartAndProduct(int $cartId,
    int $productId): ? CartItem 
    {
        return CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();
    }

    // Busca um item pelo ID do carrinho e do produto,
    // bloqueando o registro encontrado para evitar alterações simultâneas.
    public function findByCartAndProductForUpdate(int $cartId,
    int $productId): ? CartItem 
    {
        return CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();
    }

    public function create(array $cartItemData): CartItem
    {
        return CartItem::create($cartItemData);
    }

    public function update(int $id, array $cartItemData): CartItem
    {
        $cartItem = $this->find($id);
        $cartItem->update($cartItemData);

        return $cartItem->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
