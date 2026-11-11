<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\PayOrderRequest;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    // Listar pedidos do usuário autenticado
    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = $this->orderService->index($request->user()->id);

        return OrderResource::collection($orders);
    }

    // Visualizar pedido
    public function show(Request $request, int $id): OrderResource
    {
        $order = $this->orderService->show($request->user()->id, $id);

        return new OrderResource($order);
    }

    // Finalizar compra
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->checkout(
            $request->user()->id,
            $request->validated('address_id')
        );

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function pay(
        PayOrderRequest $request,
        int $id
    ): JsonResponse {
        $order = $this->orderService->pay(
            (int) auth()->id(),
            $id,
            $request->validated()['method']
        );

        return response()->json([
            'message' => 'Pagamento aprovado com sucesso.',
            'order' => new OrderResource($order),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->updateStatus(
            $id,
            $request->validated('status')
        );

        return response()->json([
            'message' => 'Status do pedido atualizado com sucesso!',
            'order'   => (new OrderResource($order))->resolve(),
        ]);
    }

    public function cancel(Request $request, int $id): OrderResource
    {
        $order = $this->orderService->cancel($request->user()->id, $id);

        return new OrderResource($order);
    }
}