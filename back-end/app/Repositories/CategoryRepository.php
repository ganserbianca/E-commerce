<?php

namespace App\Repositories;

use App\Exceptions\CategoryNotFoundException;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function index(): Collection
    {
        return Category::all();
    }

    public function find(int $id): Category
    {
        return Category::find($id)
            ?? throw new CategoryNotFoundException();
    }

    public function create(array $categoryData): Category
    {
        return Category::create($categoryData);
    }

    public function update(int $id, array $categoryData): Category
    {
        $category = $this->find($id);

        $category->update($categoryData);

        return $category->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
