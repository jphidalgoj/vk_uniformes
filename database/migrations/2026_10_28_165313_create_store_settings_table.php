
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (
            Blueprint $table
        ) {
            $table->id();

            // Identificador único de configuración
            $table->string('key', 40)->unique();

            // Información comercial
            $table->string('business_name', 120);
            $table->string('whatsapp_number', 20)
                ->nullable();

            $table->string('contact_email', 180)
                ->nullable();

            $table->string('city', 120)
                ->nullable();

            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
