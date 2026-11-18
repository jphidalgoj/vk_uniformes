<?php

use App\Models\Category;
use Database\Seeders\CategorySeeder;

test('seeds the five active catalog categories', function () {
    $this->seed(CategorySeeder::class);

    $this->assertDatabaseCount('categories', 5);
    $this->assertDatabaseHas('categories', ['slug' => 'scrub-basico', 'name' => 'Scrub Básico', 'is_active' => true, 'sort_order' => 1]);
    $this->assertDatabaseHas('categories', ['slug' => 'scrub-tendencia', 'sort_order' => 2]);
    $this->assertDatabaseHas('categories', ['slug' => 'batas', 'sort_order' => 3]);
    $this->assertDatabaseHas('categories', ['slug' => 'gorritos', 'sort_order' => 4]);
    $this->assertDatabaseHas('categories', ['slug' => 'disenos-estampados', 'sort_order' => 5]);
});

test('reseeding preserves existing categories without duplicates', function () {
    $this->seed(CategorySeeder::class);
    Category::where('slug', 'batas')->firstOrFail()->update(['name' => 'Batas personalizadas', 'is_active' => false]);

    $this->seed(CategorySeeder::class);

    $this->assertDatabaseCount('categories', 5);
    $this->assertDatabaseHas('categories', ['slug' => 'batas', 'name' => 'Batas personalizadas', 'is_active' => false]);
});
