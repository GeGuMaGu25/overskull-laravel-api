<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Business logic for products.
 */
class ProductService
{
    /**
     * @param  array{search?: string|null, category_id?: int|string|null}  $filters
     * @return Collection<int, Product>
     */
    public function list(array $filters = []): Collection
    {
        return Product::query()
            ->with('category')
            ->when($filters['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->orderByDesc('id')
            ->get();
    }

    public function find(Product $product): Product
    {
        return $product->load('category');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product
    {
        return Product::create($data)->load('category');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->load('category');
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
