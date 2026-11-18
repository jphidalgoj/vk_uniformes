<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();

            // Identificador de la sección
            $table->string('key')->unique();

            // Contenido editable
            $table->string('eyebrow')->nullable();
            $table->string('title', 200);
            $table->text('description')->nullable();

            // Imagen principal
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();

            // Llamada a la acción
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();

            // Configuración
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
