<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandController extends Controller
{
    public function __construct(
        private readonly BrandService $brandService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return BrandResource::collection($this->brandService->index());
    }

    public function show(int $id): BrandResource
    {
        return new BrandResource($this->brandService->show($id));
    }

    public function store(StoreBrandRequest $request): JsonResponse
    {
        $brand = $this->brandService->create($request->validated());

        return (new BrandResource($brand))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateBrandRequest $request, int $id): BrandResource
    {
        $brand = $this->brandService->update($id, $request->validated());

        return new BrandResource($brand);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->brandService->delete($id);

        return response()->json([
            'message' => 'Marca excluída com sucesso.',
        ]);
    }
}