<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['category_id' => CategoryFactory::new(), 'name' => fake()->words(3, true), 'slug' => fake()->unique()->slug(), 'price' => 35.50, 'is_active' => true];
    }
}
