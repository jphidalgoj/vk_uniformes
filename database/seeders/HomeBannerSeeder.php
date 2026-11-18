<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class HomeBannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::firstOrCreate(
            [
                'key' => 'home-main',
            ],
            [
                'eyebrow' => 'VK UNIFORMES',
                'title' => 'Viste tu vocación con estilo.',
                'description' => 'Descubre nuestras colecciones de uniformes profesionales, con opciones para diferentes estilos y jornadas de trabajo.',
                'image_path' => null,
                'image_alt' => null,
                'button_text' => 'Explorar colección',
                'button_url' => '/catalogo',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }
}
