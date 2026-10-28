<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_products_with_their_category(): void
    {
        Product::factory()->count(3)->create();

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'price', 'stock', 'category_id', 'category' => ['id', 'name']]]]);
    }

    public function test_it_filters_products_by_search_and_category(): void
    {
        $category = Category::factory()->create();
        Product::factory()->for($category)->create(['name' => 'Camiseta Overskull']);
        Product::factory()->create(['name' => 'Gorra Negra']);

        $this->getJson('/api/products?search=camiseta')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Camiseta Overskull');

        $this->getJson("/api/products?category_id={$category->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_it_creates_a_product(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/api/products', [
            'name' => 'Camiseta Overskull',
            'description' => 'Camiseta negra edición limitada',
            'price' => 89.90,
            'stock' => 50,
            'category_id' => $category->id,
        ])
            ->assertCreated()
            ->assertJsonPath('message', 'Producto creado correctamente')
            ->assertJsonPath('data.price', 89.9)
            ->assertJsonPath('data.category.id', $category->id);
    }

    public function test_it_validates_product_fields(): void
    {
        $this->postJson('/api/products', [
            'name' => '',
            'price' => 0,
            'stock' => -1,
            'category_id' => 999,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('errors.name.0', 'El campo nombre es requerido')
            ->assertJsonPath('errors.price.0', 'El precio debe ser mayor que 0')
            ->assertJsonPath('errors.stock.0', 'El stock no puede ser negativo')
            ->assertJsonPath('errors.category_id.0', 'La categoría seleccionada no existe');
    }

    public function test_it_requires_a_numeric_price(): void
    {
        $category = Category::factory()->create();

        $this->postJson('/api/products', [
            'name' => 'Polo',
            'price' => 'abc',
            'stock' => 1,
            'category_id' => $category->id,
        ])
            ->assertUnprocessable()
            ->assertJsonPath('errors.price', ['El precio debe ser numérico']);
    }

    public function test_it_updates_a_product(): void
    {
        $product = Product::factory()->create();

        $this->putJson("/api/products/{$product->id}", [
            'name' => 'Producto editado',
            'price' => 120.50,
            'stock' => 10,
            'category_id' => $product->category_id,
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Producto editado')
            ->assertJsonPath('data.price', 120.5);
    }

    public function test_it_deletes_a_product(): void
    {
        $product = Product::factory()->create();

        $this->deleteJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Producto eliminado correctamente');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_it_returns_404_for_missing_products(): void
    {
        $this->getJson('/api/products/999')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }
}
