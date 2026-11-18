<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Scrub Básico', 'scrub-basico'],
            ['Scrub Tendencia', 'scrub-tendencia'],
            ['Batas', 'batas'],
            ['Gorritos', 'gorritos'],
            ['Diseños estampados', 'disenos-estampados'],
        ];

        foreach ($categories as $index => $category) {
            Category::firstOrCreate(
                ['slug' => $category[1]],
                [
                    'name' => $category[0],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
