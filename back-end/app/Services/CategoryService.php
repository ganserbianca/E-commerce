<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    ) {}

    public function index(): Collection
    {
        return $this->categoryRepository->index();
    }

    public function show(int $id): Category
    {
        return $this->categoryRepository->find($id);
    }

    public function create(array $categoryData): Category
    {
        return $this->categoryRepository->create($categoryData);
    }

    public function update(int $id, array $categoryData): Category
    {
        return $this->categoryRepository->update($id, $categoryData);
    }

    public function delete(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }
}
