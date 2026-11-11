<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AddressController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $addresses = $this->addressService->index($request->user()->id);

        return AddressResource::collection($addresses);
    }

    public function show(Request $request, int $id): AddressResource
    {
        $address = $this->addressService->show(
            $request->user()->id,
            $id
        );

        return new AddressResource($address);
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $address = $this->addressService->create(
            $request->user()->id,
            $request->validated()
        );

        return (new AddressResource($address))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateAddressRequest $request, int $id): AddressResource
    {
        $address = $this->addressService->update(
            $request->user()->id,
            $id,
            $request->validated()
        );

        return new AddressResource($address);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->addressService->delete(
            $request->user()->id,
            $id
        );

        return response()->json([
            'message' => 'Endereço excluído com sucesso.',
        ]);
    }
}
