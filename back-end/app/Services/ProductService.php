<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {}

    public function index(): Collection
    {
        return $this->productRepository->index();
    }

    public function show(int $id): Product
    {
        return $this->productRepository->find($id);
    }

    public function create(array $productData): Product
    {
        $audienceIds = $productData['audience_ids'] ?? null;

        unset($productData['audience_ids']);

        $product = $this->productRepository->create($productData);

        if ($audienceIds !== null) {
            $this->productRepository->syncAudiences(
                $product,
                $audienceIds
            );
        }

        return $this->show($product->id);
    }

    public function update(int $id, array $productData): Product
    {
        $syncAudiences = array_key_exists(
            'audience_ids',
            $productData
        );

        $audienceIds = $productData['audience_ids'] ?? [];

        unset($productData['audience_ids']);

        $product = $this->productRepository->update(
            $id,
            $productData
        );

        if ($syncAudiences) {
            $this->productRepository->syncAudiences(
                $product,
                $audienceIds
            );
        }

        return $this->show($product->id);
    }

    public function delete(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}