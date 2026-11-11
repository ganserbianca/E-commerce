<?php

namespace App\Repositories;

use App\Exceptions\CartNotFoundException;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Collection;

class CartRepository
{
    public function index(): Collection
    {
        return Cart::all();
    }

    public function find(int $id): Cart
    {
        return Cart::find($id)
            ?? throw new CartNotFoundException();
    }

    public function findByUserWithItems(int $userId): ?Cart
    {
        return Cart::with('items.product')
            ->where('user_id', $userId)
            ->first();
    }

    public function findOrCreateByUser(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    public function loadItems(Cart $cart): Cart
    {
        return $cart->load('items.product');
    }

    public function create(array $cartData): Cart
    {
        return Cart::create($cartData);
    }

    public function update(int $id, array $cartData): Cart
    {
        $cart = $this->find($id);
        $cart->update($cartData);

        return $cart->refresh();
    }

    public function clearItems(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
