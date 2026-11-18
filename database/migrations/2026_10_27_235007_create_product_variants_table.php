
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            // Relación con el producto
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Identificador único de la variante
            $table->string('sku', 70)->unique();

            // Características
            $table->string('size', 20)->default('Única');
            $table->string('color_name', 60);

            // Código hexadecimal, por ejemplo #800020
            $table->char('color_hex', 7)->nullable();

            // Inventario independiente
            $table->unsignedInteger('stock')->default(0);

            // Activar o desactivar la variante
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Evita repetir la misma talla y color
            // dentro de un mismo producto.
            $table->unique(
                ['product_id', 'size', 'color_name'],
                'product_variant_combination_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
