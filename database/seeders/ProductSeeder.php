<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            'Polos' => [
                ['name' => 'Camiseta Overskull', 'description' => 'Camiseta negra edición limitada', 'price' => 89.90, 'stock' => 50],
                ['name' => 'Polo Oversize Skull', 'description' => 'Polo oversize de algodón pima', 'price' => 79.90, 'stock' => 35],
                ['name' => 'Polo Básico Blanco', 'description' => 'Polo básico de cuello redondo', 'price' => 49.90, 'stock' => 8],
            ],
            'Poleras' => [
                ['name' => 'Polerón Overskull', 'description' => 'Polerón con capucha y bordado frontal', 'price' => 159.90, 'stock' => 20],
                ['name' => 'Casaca Bomber', 'description' => 'Casaca bomber negra con forro interno', 'price' => 219.90, 'stock' => 5],
            ],
            'Pantalones' => [
                ['name' => 'Jogger Urbano', 'description' => 'Jogger de algodón con puños elásticos', 'price' => 119.90, 'stock' => 25],
                ['name' => 'Cargo Militar', 'description' => 'Pantalón cargo con seis bolsillos', 'price' => 139.90, 'stock' => 12],
            ],
            'Accesorios' => [
                ['name' => 'Gorra Overskull', 'description' => 'Gorra snapback bordada', 'price' => 49.90, 'stock' => 40],
                ['name' => 'Mochila Urbana', 'description' => 'Mochila impermeable con porta laptop', 'price' => 149.90, 'stock' => 3],
            ],
        ];

        foreach ($products as $categoryName => $items) {
            $category = Category::where('name', $categoryName)->firstOrFail();

            foreach ($items as $item) {
                Product::updateOrCreate(
                    ['name' => $item['name']],
                    [...$item, 'category_id' => $category->id],
                );
            }
        }
    }
}
