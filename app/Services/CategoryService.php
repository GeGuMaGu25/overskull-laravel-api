<?php

namespace App\Services;

use App\Exceptions\CategoryHasProductsException;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

/**
 * Business logic for categories.
 */
class CategoryService
{
    /**
     * @return Collection<int, Category>
     */
    public function list(): Collection
    {
        return Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }

    public function find(Category $category): Category
    {
        return $category->loadCount('products');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Category
    {
        return Category::create($data)->loadCount('products');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category->loadCount('products');
    }

    /**
     * @throws CategoryHasProductsException
     */
    public function delete(Category $category): void
    {
        if ($category->products()->exists()) {
            throw new CategoryHasProductsException;
        }

        $category->delete();
    }
}
