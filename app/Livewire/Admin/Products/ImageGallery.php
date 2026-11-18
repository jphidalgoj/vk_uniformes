<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
class ImageGallery extends Component
{
    use WithFileUploads;

    public Product $product;

    public array $photos = [];

    public string $photoColor = '';

    public string $notice = '';

    private const MAX_IMAGES = 48;

    public const MAX_IMAGES_PER_COLOR = 4;

    public const MAX_GENERAL_IMAGES = 8;

    // ========================================
    // INICIALIZACIÓN
    // ========================================

    public function mount(Product $product): void
    {
        Gate::authorize('access-admin');

        $this->product = $product;
        // Default to first variant color if available
        $firstColor = $this->product->variants()->first()?->color_name;
        if ($firstColor) {
            $this->photoColor = $firstColor;
        }
    }

    public function selectColor(string $color): void
    {
        $this->photoColor = $color;
        $this->resetValidation();
    }

    // ========================================
    // GUARDAR FOTOGRAFÍAS
    // ========================================

    public function savePhotos(): void
    {
        Gate::authorize('access-admin');

        $this->validate([
            'photoColor' => ['nullable', 'string', Rule::in($this->product->variants()->pluck('color_name')->all())],
            'photos' => [
                'required',
                'array',
                'min:1',
                'max:8',
            ],

            'photos.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:3072',
            ],
        ], [
            'photos.required' => 'Selecciona al menos una fotografía.',

            'photos.max' => 'Puedes seleccionar máximo 8 fotografías.',

            'photos.*.image' => 'Selecciona archivos de imagen válidos.',

            'photos.*.mimes' => 'Solo se permiten imágenes JPG, PNG o WebP.',

            'photos.*.max' => 'Cada imagen debe pesar máximo 3 MB.',
        ]);

        $storedPaths = [];

        try {
            DB::transaction(function () use (&$storedPaths) {

                // Bloqueo para evitar que dos operaciones
                // superen el límite de fotografías.
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($this->product->id);

                $currentCount = $product->images()->count();

                if (
                    $currentCount + count($this->photos)
                    > self::MAX_IMAGES
                ) {
                    throw ValidationException::withMessages([
                        'photos' => 'El producto admite máximo 48 fotografías.',
                    ]);
                }

                $targetColor = $this->photoColor ?: null;
                $colorCount = $product->images()
                    ->where(function ($query) use ($targetColor) {
                        if ($targetColor === null) {
                            $query->whereNull('color_name')->orWhere('color_name', '');
                        } else {
                            $query->where('color_name', $targetColor);
                        }
                    })
                    ->count();

                $limit = $targetColor === null ? self::MAX_GENERAL_IMAGES : self::MAX_IMAGES_PER_COLOR;

                if ($colorCount + count($this->photos) > $limit) {
                    $colorLabel = $targetColor ? 'El color "'.$targetColor.'"' : 'Las fotografías generales';
                    throw ValidationException::withMessages([
                        'photos' => "{$colorLabel} admiten un máximo de ".$limit." fotos (actualmente tiene {$colorCount}).",
                    ]);
                }

                $lastOrder = (int) $product->images()
                    ->max('sort_order');

                $hasPrimary = $product->images()
                    ->where('is_primary', true)
                    ->exists();

                foreach ($this->photos as $photo) {

                    $path = $photo->store(
                        'products/'.$product->id,
                        'public'
                    );

                    if ($path === false) {
                        throw new \RuntimeException(
                            'No se pudo guardar una fotografía.'
                        );
                    }

                    $storedPaths[] = $path;

                    $lastOrder++;

                    $product->images()->create([
                        'path' => $path,
                        'color_name' => $this->photoColor ?: null,

                        'alt_text' => $product->name.
                            ' - fotografía '.$lastOrder,

                        'sort_order' => $lastOrder,

                        'is_primary' => ! $hasPrimary,
                    ]);

                    // Solo la primera imagen sin
                    // portada previa será principal.
                    $hasPrimary = true;
                }
            });

        } catch (\Throwable $exception) {

            // Si falla la base de datos,
            // borrar archivos recién almacenados.
            if (! empty($storedPaths)) {
                Storage::disk('public')
                    ->delete($storedPaths);
            }

            throw $exception;
        }

        $this->reset('photos');

        $this->notice =
            'Fotografías guardadas correctamente.';
    }

    // ========================================
    // ESTABLECER IMAGEN PRINCIPAL
    // ========================================

    public function setPrimary(int $imageId): void
    {
        Gate::authorize('access-admin');

        DB::transaction(function () use ($imageId) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($this->product->id);

            // Verifica que la imagen corresponda
            // al producto actual.
            $image = $product->images()
                ->findOrFail($imageId);

            // Quitar la marca principal anterior.
            $product->images()->update([
                'is_primary' => false,
            ]);

            // Establecer nueva portada.
            $image->update([
                'is_primary' => true,
            ]);
        });

        $this->notice =
            'Fotografía principal actualizada.';
    }

    public function setPhotoColor(int $imageId, string $color): void
    {
        Gate::authorize('access-admin');
        validator(['color' => $color], [
            'color' => ['nullable', 'string', Rule::in($this->product->variants()->pluck('color_name')->all())],
        ])->validate();
        DB::transaction(function () use ($imageId, $color) {
            $product = Product::query()->lockForUpdate()->findOrFail($this->product->id);
            $image = $product->images()->findOrFail($imageId);
            $targetColor = $color ?: null;
            $count = $product->images()->where('id', '!=', $imageId)
                ->where(function ($query) use ($targetColor) {
                    if ($targetColor === null) {
                        $query->whereNull('color_name')->orWhere('color_name', '');
                    } else {
                        $query->where('color_name', $targetColor);
                    }
                })->count();
            $limit = $targetColor === null ? self::MAX_GENERAL_IMAGES : self::MAX_IMAGES_PER_COLOR;
            if ($count >= $limit) {
                throw ValidationException::withMessages(['color' => "Este grupo admite máximo {$limit} fotografías."]);
            }
            $image->update(['color_name' => $targetColor]);
        });
        $this->notice = 'Color de la fotografía actualizado.';
    }

    // ========================================
    // CAMBIAR ORDEN DE FOTOGRAFÍAS
    // ========================================

    public function movePhoto(
        int $imageId,
        string $direction
    ): void {
        Gate::authorize('access-admin');

        abort_unless(
            in_array($direction, ['up', 'down'], true),
            422
        );

        DB::transaction(function () use (
            $imageId,
            $direction
        ) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($this->product->id);

            $images = $product->images()
                ->get()
                ->values();

            $index = $images->search(
                fn ($image) => $image->id === $imageId
            );

            abort_if($index === false, 404);

            $target = $direction === 'up'
                ? $index - 1
                : $index + 1;

            if (
                $target < 0 ||
                $target >= $images->count()
            ) {
                return;
            }

            $ids = $images->pluck('id')->all();

            // Intercambiar posiciones.
            [$ids[$index], $ids[$target]] = [
                $ids[$target],
                $ids[$index],
            ];

            // Actualizar el orden de toda la galería.
            foreach ($ids as $position => $id) {
                $product->images()
                    ->whereKey($id)
                    ->update([
                        'sort_order' => $position + 1,
                    ]);
            }
        });

        $this->notice =
            'Orden de fotografías actualizado.';
    }

    // ========================================
    // ELIMINAR UNA FOTOGRAFÍA
    // ========================================

    public function deletePhoto(int $imageId): void
    {
        Gate::authorize('access-admin');

        $path = DB::transaction(function () use ($imageId) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($this->product->id);

            $image = $product->images()
                ->findOrFail($imageId);

            $path = $image->path;
            $wasPrimary = $image->is_primary;

            $image->delete();

            // Si eliminamos la portada,
            // designamos la siguiente fotografía.
            if ($wasPrimary) {

                $nextImage = $product->images()->first();

                if ($nextImage) {
                    $nextImage->update([
                        'is_primary' => true,
                    ]);
                }
            }

            return $path;
        });

        // Solo eliminar el archivo físico
        // después de completar la operación en MySQL.
        Storage::disk('public')->delete($path);

        $this->notice =
            'Fotografía eliminada correctamente.';
    }

    // ========================================
    // MOSTRAR GALERÍA
    // ========================================

    public function render()
    {
        $images = $this->product->images()->get();
        $variants = $this->product->variants()->get();
        $colors = $variants->pluck('color_name')->unique()->filter()->values();

        $colorsData = $colors->map(function ($colorName) use ($variants, $images) {
            $variant = $variants->firstWhere('color_name', $colorName);
            $colorImages = $images->where('color_name', $colorName)->values();

            return [
                'name' => $colorName,
                'hex' => $variant?->color_hex ?: '#331344',
                'images' => $colorImages,
                'count' => $colorImages->count(),
                'remaining' => max(0, self::MAX_IMAGES_PER_COLOR - $colorImages->count()),
            ];
        });

        $generalImages = $images->filter(fn ($img) => empty($img->color_name))->values();

        return view(
            'livewire.admin.products.image-gallery',
            [
                'images' => $images,
                'colors' => $colors,
                'colorsData' => $colorsData,
                'generalImages' => $generalImages,
                'maxPerColor' => self::MAX_IMAGES_PER_COLOR,
                'maxGeneral' => self::MAX_GENERAL_IMAGES,
                'selectedLimit' => $this->photoColor === '' ? self::MAX_GENERAL_IMAGES : self::MAX_IMAGES_PER_COLOR,
                'remaining' => max(
                    0,
                    self::MAX_IMAGES - $images->count()
                ),
            ]
        );
    }
}
