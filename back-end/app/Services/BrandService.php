<?php

namespace App\Services;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Database\Eloquent\Collection;

class BrandService
{
    public function __construct(
        private readonly BrandRepository $brandRepository
    ) {}

    public function index(): Collection
    {
        return $this->brandRepository->index();
    }

    public function show(int $id): Brand
    {
        return $this->brandRepository->find($id);
    }

    public function create(array $brandData): Brand
    {
        return $this->brandRepository->create($brandData);
    }

    public function update(int $id, array $brandData): Brand
    {
        return $this->brandRepository->update($id, $brandData);
    }

    public function delete(int $id): bool
    {
        return $this->brandRepository->delete($id);
    }
}