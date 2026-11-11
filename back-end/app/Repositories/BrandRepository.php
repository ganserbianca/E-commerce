<?php

namespace App\Repositories;

use App\Exceptions\BrandNotFoundException;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository
{
    public function index(): Collection
    {
        return Brand::all();
    }

    public function find(int $id): Brand
    {
        return Brand::find($id) ?? throw new BrandNotFoundException();
    }

    public function create(array $brandData): Brand
    {
        return Brand::create($brandData);
    }

    public function update(int $id, array $brandData): Brand
    {
        $brand = $this->find($id);
        $brand->update($brandData);

        return $brand->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}