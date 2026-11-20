<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Product;
use Illuminate\Database\Seeder;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        $blocks = [
            ['home-main', 'hero', 'Uniformes que inspiran profesionalismo', 'slider/uniformes-inspiran-profesionalismo.png', ''],
            ['home-video', 'video', 'Conoce VK Uniformes', '', ''],
            ['home-categories', 'categories', 'Categorías', '', ''],
            ['home-ribknit', 'products', 'Conoce la colección RibKnit', 'slider/coleccion-ribknit-profesional.png', 'ribknit'],
            ['home-borgona', 'products', 'Estilo en Borgoña', 'secciones/uniformes-vk-estilo-borgona.png', 'granada'],
            ['home-embroidery', 'banner', 'Personaliza tu uniforme', '', ''],
            ['home-outfits', 'products', 'Conjuntos', 'secciones/uniformes-medicos-azul-rosa.png', 'conjunto'],
            ['home-caps', 'products', 'Gorritos con estilo y color', 'slider/eleva-tu-look-gorritos.png', 'gorro'],
            ['home-clinical', 'products', 'Conjunto clínico · Comodidad total', 'secciones/conjunto-clinico-comodidad.png', 'experto'],
            ['home-sales', 'products', 'Rebajas', '', 'sale'],
            ['home-lines', 'lines', 'Encuentra tu estilo', '', ''],
            ['home-contact', 'banner', 'Uniformes para todo tu equipo', '', ''],
        ];

        foreach ($blocks as $index => [$key, $type, $title, $asset, $search]) {
            $products = Product::query()->where('is_active', true);
            if ($search === 'sale') {
                $products->whereColumn('compare_price', '>', 'price');
            } elseif ($search !== '') {
                $products->where('name', 'like', '%'.$search.'%');
            } else {
                $products->whereRaw('1 = 0');
            }
            $content = ['type' => $type, 'asset' => $asset, 'product_ids' => $products->orderBy('sort_order')->limit(8)->pluck('id')->all(), 'video_url' => '', 'image_only' => true];
            $block = Banner::firstOrCreate(['key' => $key], [
                'title' => $title, 'button_text' => 'Ver colección', 'button_url' => '/catalogo',
                'is_active' => $type !== 'video', 'sort_order' => $index * 10, 'content' => $content,
                'description' => $key === 'home-embroidery' ? 'Consulta las opciones de personalización para tu empresa.' : ($key === 'home-contact' ? 'Encuentra el uniforme que acompaña a tu equipo cada día.' : null),
            ]);
            if ($block->content === null) {
                $block->update(['content' => $content]);
            }
            if ($key === 'home-main' && ! array_key_exists('slides', $block->content)) {
                $slides = $block->slides();
                foreach ([
                    ['slider/coleccion-ribknit-profesional.png', 'Colección RibKnit profesional'],
                    ['slider/eleva-tu-look-gorritos.png', 'Gorritos VK con estilo y color'],
                ] as [$slideAsset, $slideAlt]) {
                    $slides[] = ['path' => '', 'asset' => $slideAsset, 'alt' => $slideAlt];
                }
                $block->update(['content' => array_merge($block->content, ['slides' => $slides])]);
            }
        }
    }
}
