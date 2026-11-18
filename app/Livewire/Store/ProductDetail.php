<?php

namespace App\Livewire\Store;

use App\Models\Product;
use App\Models\StoreSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts::app')]
class ProductDetail extends Component
{
    // Identificador protegido del producto
    #[Locked]
    public int $productId;

    // Fotografía seleccionada
    public ?int $selectedImageId = null;

    // Variante elegida por el cliente
    public string $selectedColor = '';

    public string $selectedSize = '';

    // ========================================
    // CARGAR PRODUCTO POR SLUG
    // ========================================
    public function mount(string $slug): void
    {
        $product = Product::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->firstOrFail();

        $this->productId = $product->id;
        $requestedColor = request()->query('color', '');
        $colors = $product->variants()
            ->where('is_active', true)
            ->whereNotNull('color_name')
            ->where('color_name', '!=', '')
            ->pluck('color_name')
            ->unique()
            ->values();

        $cover = $product->images->firstWhere('is_primary', true) ?? $product->images->first();

        if (is_string($requestedColor) && $requestedColor !== '' && $colors->contains($requestedColor)) {
            $this->selectedColor = $requestedColor;
        } elseif ($cover?->color_name && $colors->contains($cover->color_name)) {
            $this->selectedColor = $cover->color_name;
        } else {
            $this->selectedColor = $colors->first() ?? '';
        }
    }

    // ========================================
    // CAMBIAR COLOR
    // ========================================
    public function selectColor(string|int $colorOrVariantId): void
    {
        if (is_numeric($colorOrVariantId)) {
            $variant = $this->getVisibleProduct()->variants()
                ->where('is_active', true)
                ->findOrFail((int) $colorOrVariantId);
            $this->selectedColor = (string) ($variant->color_name ?? '');
        } else {
            abort_unless(
                $this->getVisibleProduct()->variants()
                    ->where('is_active', true)
                    ->where('color_name', (string) $colorOrVariantId)
                    ->exists(),
                404
            );
            $this->selectedColor = (string) $colorOrVariantId;
        }

        $this->updatedSelectedColor();
    }

    public function selectSize(string $size): void
    {
        $this->selectedSize = ($this->selectedSize === $size) ? '' : $size;
        $this->updatedSelectedSize();
    }

    public function updatedSelectedSize(): void
    {
        if ($this->selectedSize === '') {
            return;
        }

        $query = $this->getVisibleProduct()->variants()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('size', $this->selectedSize)
                    ->orWhere('size', 'like', $this->selectedSize.',%')
                    ->orWhere('size', 'like', '%, '.$this->selectedSize.',%')
                    ->orWhere('size', 'like', '%, '.$this->selectedSize)
                    ->orWhere('size', 'like', '%,'.$this->selectedSize);
            });

        if ($this->selectedColor !== '') {
            $query->where('color_name', $this->selectedColor);
        }

        if (! $query->exists()) {
            $this->selectedSize = '';
        }
    }

    public function updatedSelectedColor(): void
    {
        // Al cambiar el color debemos resetear
        // la talla seleccionada y la imagen activa
        $this->selectedSize = '';
        $this->selectedImageId = null;

        if ($this->selectedColor !== '') {
            $exists = $this->getVisibleProduct()->variants()
                ->where('is_active', true)
                ->where('color_name', $this->selectedColor)
                ->exists();

            if (! $exists) {
                $this->selectedColor = '';
            }
        }
    }

    // ========================================
    // CAMBIAR FOTOGRAFÍA PRINCIPAL VISIBLE
    // ========================================
    public function selectImage(int $imageId): void
    {
        $product = $this->getVisibleProduct();

        // No permitir seleccionar una imagen
        // que pertenezca a otro producto.
        abort_unless($product->imagesForColor($this->selectedColor)->contains('id', $imageId), 404);

        $this->selectedImageId = $imageId;
    }

    // ========================================
    // PRODUCTO VISIBLE EN LA TIENDA
    // ========================================
    private function getVisibleProduct(): Product
    {
        return Product::query()
            ->whereKey($this->productId)
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('is_active', true);
            })
            ->with(['category', 'images'])
            ->firstOrFail();
    }

    // ========================================
    // ENLACE DE WHATSAPP CON MENSAJE
    // ========================================
    private function whatsappUrl(
        string $phone,
        string $message
    ): string {
        return 'https://wa.me/'
            .$phone
            .'?text='
            .rawurlencode($message);
    }

    // ========================================
    // RENDERIZAR VISTA
    // ========================================

    public function render()
    {
        // ========================================
        // INFORMACIÓN DEL PRODUCTO
        // ========================================

        $product = $this->getVisibleProduct();

        $images = $product->imagesForColor($this->selectedColor);

        $activeImage = $images->firstWhere(
            'id',
            $this->selectedImageId
        ) ?? $images->firstWhere(
            'is_primary',
            true
        ) ?? $images->first();

        // ========================================
        // VARIANTES
        // ========================================

        $variants = $product->variants()
            ->where('is_active', true)
            ->orderBy('color_name')
            ->orderBy('size')
            ->get();

        $colors = $variants
            ->pluck('color_name')
            ->filter(fn ($c) => ! empty($c))
            ->unique()
            ->values();

        $candidateVariants = ($colors->isNotEmpty() && $this->selectedColor !== '')
            ? $variants->where('color_name', $this->selectedColor)->values()
            : $variants;

        // Descomponer posibles tallas múltiples ("S, M, L") en botones individuales
        $sizesMap = collect();
        foreach ($candidateVariants as $v) {
            $individualSizes = array_filter(array_map('trim', explode(',', (string) $v->size)));
            foreach ($individualSizes as $indSize) {
                if (! $sizesMap->has($indSize)) {
                    $sizesMap->put($indSize, [
                        'id' => $v->id,
                        'size' => $indSize,
                        'stock' => (int) $v->stock,
                        'color_name' => $v->color_name,
                    ]);
                } else {
                    // Si ya existe la talla, sumar o mantener stock
                    $existing = $sizesMap->get($indSize);
                    $existing['stock'] += (int) $v->stock;
                    $sizesMap->put($indSize, $existing);
                }
            }
        }
        $sizes = $sizesMap->values();

        $selectedVariant = $variants->first(function ($variant) use ($colors) {
            $variantSizes = array_filter(array_map('trim', explode(',', (string) $variant->size)));
            $hasSize = in_array($this->selectedSize, $variantSizes, true) || $variant->size === $this->selectedSize;

            if ($colors->isNotEmpty() && $this->selectedColor !== '') {
                return $variant->color_name === $this->selectedColor && $hasSize;
            }

            return $hasSize;
        });

        $hasStock = $variants->contains(function ($variant) {
            return $variant->stock > 0;
        });

        // ========================================
        // CONFIGURACIÓN DE WHATSAPP
        // ========================================

        $storeSettings = StoreSetting::query()
            ->where('key', 'main')
            ->first();

        $businessName = $storeSettings?->business_name
            ?: 'VK Uniformes';

        $phoneSource = $storeSettings
            ? ($storeSettings->whatsapp_number ?? '')
            : config('vk.whatsapp_number', '');

        $phone = preg_replace(
            '/\D+/',
            '',
            (string) $phoneSource
        );

        $whatsappEnabled = preg_match(
            '/^[1-9][0-9]{7,14}$/',
            $phone
        ) === 1;

        $informationUrl = null;
        $quotationUrl = null;

        if ($whatsappEnabled) {

            $productUrl = route('store.product', [
                'slug' => $product->slug,
            ]);

            // ====================================
            // MENSAJE PARA SOLICITAR INFORMACIÓN
            // ====================================

            $informationLines = [
                'Hola, '.$businessName.'.',
                '',
                'Quisiera recibir más información sobre este uniforme.',
                '',
                'Producto: '.$product->name,
                'Categoría: '.$product->category->name,
            ];

            if ($selectedVariant) {
                $informationLines[] =
                    'Diseño / color: '.($selectedVariant->color_name ?: 'Personalizado');

                $informationLines[] =
                    'Talla: '.$selectedVariant->size;
            } elseif ($this->selectedSize !== '') {
                $informationLines[] = 'Talla de interés: '.$this->selectedSize;
            } elseif ($this->selectedColor !== '') {
                $informationLines[] = 'Diseño / color de interés: '.$this->selectedColor;
            }

            $informationLines[] = '';
            $informationLines[] = 'Enlace: '.$productUrl;
            $informationLines[] = '';
            $informationLines[] =
                '¿Podrían brindarme más detalles?';

            $informationUrl = $this->whatsappUrl(
                $phone,
                implode("\n", $informationLines)
            );

            // ====================================
            // MENSAJE PARA SOLICITAR COTIZACIÓN
            // ====================================

            if (
                $selectedVariant &&
                (int) $selectedVariant->stock > 0
            ) {
                $colorDisplay = $selectedVariant->color_name ?: ($this->selectedColor ?: 'Personalizado / Diseño único');
                $quotationLines = [
                    'Hola, '.$businessName.'.',
                    '',
                    'Me gustaría solicitar una cotización del siguiente uniforme:',
                    '',
                    'Producto: '.$product->name,
                    'Categoría: '.$product->category->name,
                    'Talla: '.$selectedVariant->size,
                    'Diseño / color: '.$colorDisplay,
                    'Código: '.$selectedVariant->sku,
                    'Precio publicado: $'.number_format(
                        (float) $product->price,
                        2,
                        '.',
                        ''
                    ),
                    '',
                    'Enlace del producto: '.$productUrl,
                    '',
                    '¿Podrían confirmarme el precio final, la disponibilidad y las opciones de entrega?',
                ];

                $quotationUrl = $this->whatsappUrl(
                    $phone,
                    implode("\n", $quotationLines)
                );
            }
        }

        // ========================================
        // ENVIAR DATOS A LA VISTA
        // ========================================

        return view('livewire.store.product-detail', [
            'product' => $product,
            'images' => $images,
            'activeImage' => $activeImage,
            'colors' => $colors,
            'variants' => $variants,
            'sizes' => $sizes,
            'selectedVariant' => $selectedVariant,
            'hasStock' => $hasStock,
            'whatsappEnabled' => $whatsappEnabled,
            'informationUrl' => $informationUrl,
            'quotationUrl' => $quotationUrl,
        ])->title($product->name.' | VK Uniformes');
    }
}
