<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_categories_with_the_standard_format(): void
    {
        Category::factory()->count(2)->create();

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonStructure(['success', 'message', 'data' => [['id', 'name', 'description', 'products_count']]])
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_it_creates_a_category(): void
    {
        $this->postJson('/api/categories', ['name' => 'Polos', 'description' => 'Polos de algodón'])
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Categoría creada correctamente')
            ->assertJsonPath('data.name', 'Polos');

        $this->assertDatabaseHas('categories', ['name' => 'Polos']);
    }

    public function test_it_validates_required_name(): void
    {
        $this->postJson('/api/categories', ['name' => ''])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'El campo nombre es requerido')
            ->assertJsonPath('errors.name.0', 'El campo nombre es requerido');
    }

    public function test_it_rejects_duplicated_names(): void
    {
        Category::factory()->create(['name' => 'Polos']);

        $this->postJson('/api/categories', ['name' => 'Polos'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_it_updates_a_category(): void
    {
        $category = Category::factory()->create();

        $this->putJson("/api/categories/{$category->id}", ['name' => 'Nuevo nombre'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nuevo nombre');
    }

    public function test_it_deletes_a_category_without_products(): void
    {
        $category = Category::factory()->create();

        $this->deleteJson("/api/categories/{$category->id}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_it_does_not_delete_a_category_with_products(): void
    {
        $product = Product::factory()->create();

        $this->deleteJson("/api/categories/{$product->category_id}")
            ->assertConflict()
            ->assertJsonPath('success', false);

        $this->assertDatabaseHas('categories', ['id' => $product->category_id]);
    }

    public function test_it_returns_404_for_missing_categories(): void
    {
        $this->getJson('/api/categories/999')
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'message' => 'Recurso no encontrado',
                'errors' => null,
            ]);
    }
}
