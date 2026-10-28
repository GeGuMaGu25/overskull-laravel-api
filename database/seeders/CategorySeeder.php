<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Polos', 'description' => 'Polos y camisetas de la colección Overskull'],
            ['name' => 'Poleras', 'description' => 'Poleras y casacas con capucha'],
            ['name' => 'Pantalones', 'description' => 'Joggers, jeans y pantalones cargo'],
            ['name' => 'Accesorios', 'description' => 'Gorras, mochilas, medias y más'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
