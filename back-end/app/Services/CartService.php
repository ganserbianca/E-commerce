<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotInCartException;
use App\Exceptions\ProductUnavailableException;
use App\Models\Cart;
use App\Repositories\CartItemRepository;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TransactionRepository;

class CartService
{
    public function __construct(
        private readonly CartRepository $cartRepository,
        private readonly CartItemRepository $cartItemRepository,
        private readonly ProductRepository $productRepository,
        private readonly TransactionRepository $transaction,
    ) {}

    // Retorna o carrinho do usuário com seus produtos
    public function index(int $userId): Cart
    {
        $cart = $this->cartRepository->findOrCreateByUser($userId);

        return $this->cartRepository->loadItems($cart);
    }

    // Adiciona um produto ao carrinho
    public function addItem(int $userId, int $productId, int $quantity): Cart
    {
        return $this->transaction->run(function () use ($userId, $productId, $quantity) {
            $product = $this->productRepository->findForUpdate($productId);

            if (!$product->available) {
                throw new ProductUnavailableException();
            }

            $cart = $this->cartRepository->findOrCreateByUser($userId);
            $item = $this->cartItemRepository
                ->findByCartAndProduct($cart->id, $productId);

            $quantityInCart = $item?->quantity ?? 0;
            $totalQuantity = $quantityInCart + $quantity;

            if ($totalQuantity > $product->stock_quantity) {
                throw new InsufficientStockException(
                    max(0, $product->stock_quantity - $quantityInCart)
                );
            }

            if ($item) {
                $this->cartItemRepository->update($item->id, [
                    'quantity' => $totalQuantity,
                ]);
            } else {
                $this->cartItemRepository->create([
                    'cart_id'    => $cart->id,
                    'product_id' => $productId,
                    'quantity'   => $quantity,
                ]);
            }

            return $this->cartRepository->loadItems($cart);
        });
    }

    // Atualiza a quantidade (zero remove o produto)
    public function updateItem(int $userId, int $productId, int $quantity): Cart
    {
        return $this->transaction->run(function () use ($userId, $productId, $quantity) {
            $cart = $this->cartRepository->findOrCreateByUser($userId);
            $item = $this->cartItemRepository
                ->findByCartAndProduct($cart->id, $productId)
                ?? throw new ProductNotInCartException();

            if ($quantity === 0) {
                $this->cartItemRepository->delete($item->id);

                return $this->cartRepository->loadItems($cart);
            }

            $product = $this->productRepository->findForUpdate($productId);

            if (!$product->available) {
                throw new ProductUnavailableException();
            }

            if ($quantity > $product->stock_quantity) {
                throw new InsufficientStockException($product->stock_quantity);
            }

            $this->cartItemRepository->update($item->id, [
                'quantity' => $quantity,
            ]);

            return $this->cartRepository->loadItems($cart);
        });
    }

    // Remove o produto do carrinho
    public function removeItem(int $userId, int $productId): Cart
    {
        return $this->updateItem($userId, $productId, 0);
    }
}