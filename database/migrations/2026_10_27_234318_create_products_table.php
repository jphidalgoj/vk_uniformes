
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Relación con categorías
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            // Información del producto
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Precios en dólares
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)
                ->nullable();

            // Configuración comercial
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // Orden de visualización
            $table->unsignedInteger('sort_order')->default(0);

            // Eliminación reversible
            $table->softDeletes();

            $table->timestamps();

            $table->index(['category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
