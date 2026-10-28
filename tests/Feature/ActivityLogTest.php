<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_update_and_delete_actions_are_audited(): void
    {
        $category = Category::factory()->create();

        $productId = $this->postJson('/api/products', [
            'name' => 'Camiseta Overskull',
            'price' => 89.90,
            'stock' => 50,
            'category_id' => $category->id,
        ])->json('data.id');

        $this->putJson("/api/products/{$productId}", [
            'name' => 'Camiseta Overskull',
            'price' => 99.90,
            'stock' => 50,
            'category_id' => $category->id,
        ]);

        $this->deleteJson("/api/products/{$productId}");

        foreach (['created', 'updated', 'deleted'] as $event) {
            $this->assertDatabaseHas('activity_log', [
                'log_name' => 'product',
                'event' => $event,
                'subject_id' => $productId,
            ]);
        }

        $this->getJson('/api/activity-logs?log_name=product')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.event', 'deleted');
    }
}
