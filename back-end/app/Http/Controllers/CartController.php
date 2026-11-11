<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    // Visualizar carrinho do usuário autenticado
    public function index(Request $request): CartResource
    {
        $cart = $this->cartService->index($request->user()->id);

        return new CartResource($cart);
    }

    // Adicionar produto ao carrinho
    public function store(AddCartItemRequest $request): JsonResponse
    {
        $cart = $this->cartService->addItem(
            $request->user()->id,
            $request->validated('product_id'),
            $request->validated('quantity')
        );

        return (new CartResource($cart))
            ->response()
            ->setStatusCode(201);
    }

    // Atualizar quantidade
    public function update(UpdateCartItemRequest $request, int $productId): CartResource
    {
        $cart = $this->cartService->updateItem(
            $request->user()->id,
            $productId,
            $request->validated('quantity')
        );

        return new CartResource($cart);
    }

    // Remover produto do carrinho
    public function destroy(Request $request, int $productId): CartResource
    {
        $cart = $this->cartService->removeItem(
            $request->user()->id,
            $productId
        );

        return new CartResource($cart);
    }
}