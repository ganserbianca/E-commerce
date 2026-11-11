<?php

namespace App\Repositories;

use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function index(): Collection
    {
        return Product::all();
    }

    public function find(int $id): Product
    {
        return Product::with(['category', 'brand', 'audiences'])
            ->find($id)
            ?? throw new ProductNotFoundException();
    }

    public function findForUpdate(int $id): Product
    {
        return Product::lockForUpdate()->find($id)
            ?? throw new ProductNotFoundException();
    }

    public function create(array $productData): Product
    {
        $product = Product::create($productData);

        return $this->find($product->id);
    }

    public function update(int $id, array $productData): Product
    {
        $product = $this->find($id);

        $product->update($productData);

        return $this->find($product->id);
    }

    //atualiza os públicos relacionados a esses produtos
    public function syncAudiences(Product $product,
    array $audienceIds): void 
    {
        $product->audiences()->sync($audienceIds);
    }

    public function decrementStock(Product $product, int $quantity): void
    {
        $product->decrement('stock_quantity', $quantity);
    }

    public function incrementStock(Product $product, int $quantity): void
    {
        $product->increment('stock_quantity', $quantity);
    }

    public function delete(int $id): bool
    {
        $product = $this->find($id);

        $product->audiences()->detach();

        return $product->delete();
    }
}
