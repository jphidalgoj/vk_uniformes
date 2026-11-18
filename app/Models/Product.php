<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'compare_price',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // Categoría del producto
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Variantes por talla y color
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Galería de imágenes
    /** @return Collection<int, ProductImage> */
    public function imagesForColor(string $color): Collection
    {
        if ($color === '') {
            return $this->images;
        }

        $images = $this->images->where('color_name', $color);

        return $images->isNotEmpty()
            ? $images->values()
            : $this->images->filter(fn (ProductImage $image): bool => ! $image->color_name)->values();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
