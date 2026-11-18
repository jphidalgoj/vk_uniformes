<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color_name',
        'color_hex',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // Cada variante pertenece a un producto.
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
