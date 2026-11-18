<?php

namespace App\Livewire\Admin\Banners;

use App\Models\Banner;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
class Edit extends Component
{
    use WithFileUploads;

    // Contenido del banner
    public string $eyebrow = '';

    public string $title = '';

    public string $description = '';

    // Fotografía
    public $image = null;

    public string $imageAlt = '';

    public bool $removeImage = false;

    // Botón
    public string $buttonText = '';

    public string $buttonUrl = '/catalogo';

    // Estado
    public bool $isActive = true;

    // Notificación
    public string $notice = '';

    // =====================================
    // CARGAR INFORMACIÓN
    // =====================================

    public function mount(): void
    {
        Gate::authorize('access-admin');

        $banner = Banner::where('key', 'home-main')
            ->firstOrFail();

        $this->eyebrow = $banner->eyebrow ?? '';
        $this->title = $banner->title;
        $this->description = $banner->description ?? '';

        $this->imageAlt = $banner->image_alt ?? '';

        $this->buttonText = $banner->button_text ?? '';
        $this->buttonUrl = $banner->button_url ?: '/catalogo';

        $this->isActive = $banner->is_active;
    }

    // =====================================
    // GUARDAR CAMBIOS
    // =====================================

    public function save(): void
    {
        Gate::authorize('access-admin');

        $this->eyebrow = trim($this->eyebrow);
        $this->title = trim($this->title);
        $this->description = trim($this->description);
        $this->imageAlt = trim($this->imageAlt);
        $this->buttonText = trim($this->buttonText);

        // Destinos permitidos para el botón.
        // Evitamos introducir URLs externas o
        // destinos arbitrarios desde el formulario.
        $allowedUrls = [
            '/',
            '/catalogo',
        ];

        $categoryUrls = Category::query()
            ->where('is_active', true)
            ->pluck('slug')
            ->map(fn ($slug) => '/catalogo/'.$slug)
            ->all();

        $allowedUrls = array_merge(
            $allowedUrls,
            $categoryUrls
        );

        $this->validate([
            'eyebrow' => [
                'nullable',
                'string',
                'max:100',
            ],
            'title' => [
                'required',
                'string',
                'max:200',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'imageAlt' => [
                'nullable',
                'string',
                'max:255',
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:3072',
            ],
            'removeImage' => [
                'boolean',
            ],
            'buttonText' => [
                'nullable',
                'string',
                'max:100',
            ],
            'buttonUrl' => [
                'required',
                'string',
                Rule::in($allowedUrls),
            ],
            'isActive' => [
                'boolean',
            ],
        ], [
            'title.required' => 'Ingresa el título principal.',

            'title.max' => 'El título no puede superar 200 caracteres.',

            'image.image' => 'Selecciona una fotografía válida.',

            'image.mimes' => 'Utiliza JPG, PNG o WebP.',

            'image.max' => 'La fotografía no puede superar 3 MB.',

            'buttonUrl.in' => 'Selecciona un destino válido.',
        ]);

        // Recuperar siempre el banner desde MySQL.
        $banner = Banner::where('key', 'home-main')
            ->firstOrFail();

        $oldImage = $banner->image_path;
        $newImage = $oldImage;

        // Subir nueva fotografía o quitar la actual.
        if ($this->image) {
            $newImage = $this->image->store(
                'banners',
                'public'
            );

            if ($newImage === false) {
                throw new \RuntimeException(
                    'No se pudo guardar la fotografía.'
                );
            }
        } elseif ($this->removeImage) {
            $newImage = null;
        }

        try {
            $content = $banner->content;
            if (isset($content['slides']) && ($content['slides'] !== [] || $newImage)) {
                $content['slides'][0] = [
                    'path' => $newImage ?? '',
                    'asset' => $content['slides'][0]['asset'] ?? $content['asset'] ?? '',
                    'alt' => $this->imageAlt ?: $this->title,
                ];
            }
            $banner->update([
                'eyebrow' => $this->eyebrow ?: null,
                'title' => $this->title,
                'description' => $this->description ?: null,
                'image_path' => $newImage,
                'image_alt' => $this->imageAlt ?: null,
                'button_text' => $this->buttonText ?: null,
                'button_url' => $this->buttonUrl,
                'is_active' => $this->isActive,
                'content' => $content,
            ]);
        } catch (\Throwable $exception) {
            // Si falla MySQL, retirar la
            // fotografía nueva que se subió.
            if ($newImage && $newImage !== $oldImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        // Retirar la imagen anterior solamente
        // después de actualizar el banner.
        if ($oldImage && $oldImage !== $newImage) {
            Storage::disk('public')->delete($oldImage);
        }

        $this->reset('image', 'removeImage');

        $this->notice =
            'Banner principal actualizado correctamente.';
    }

    // =====================================
    // MOSTRAR VISTA
    // =====================================

    public function render()
    {
        $banner = Banner::where('key', 'home-main')
            ->firstOrFail();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return view('livewire.admin.banners.edit', [
            'banner' => $banner,
            'categories' => $categories,
        ]);
    }
}
