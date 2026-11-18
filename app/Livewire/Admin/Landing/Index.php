<?php

namespace App\Livewire\Admin\Landing;

use App\Models\Banner;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
class Index extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?int $sectionId = null;

    public string $title = '';

    public string $description = '';

    public string $buttonText = '';

    public string $buttonUrl = '/catalogo';

    public string $imageAlt = '';

    public string $asset = '';

    public string $videoUrl = '';

    public string $type = 'banner';

    public string $highlightText = '';

    public string $imagePosition = 'left';

    public bool $isActive = true;

    public bool $imageOnly = true;

    public bool $removeImage = false;

    public int $sortOrder = 0;

    public array $productIds = [];

    public $image = null;

    public string $notice = '';

    public array $site = [];

    public array $slideAssets = ['', '', '', ''];

    public array $slideAlts = ['', '', '', ''];

    public array $slideUrls = ['', '', '', ''];

    public array $slideUploads = [];

    public array $removeSlides = [false, false, false, false];

    #[Locked]
    public array $slidePaths = ['', '', '', ''];

    public array $videoUploads = [];

    public array $videoUrls = ['', '', ''];

    public array $videoTitles = ['', '', ''];

    public array $removeVideos = [false, false, false];

    #[Locked]
    public array $videoPaths = ['', '', ''];

    public function mount(): void
    {
        Gate::authorize('access-admin');
        $this->site = array_merge([
            'announcement' => 'VK Uniformes · Colecciones profesionales',
            'logo' => 'images/logo-vk.png',
            'footer_text' => 'Uniformes profesionales para acompañarte cada día.',
            'hours' => '', 'instagram' => '', 'facebook' => '',
            'privacy' => '', 'terms' => '',
        ], StoreSetting::where('key', 'main')->first()?->storefront ?? []);
    }

    public function edit(int $id): void
    {
        Gate::authorize('access-admin');
        $section = Banner::where('key', 'like', 'home-%')->findOrFail($id);
        $this->resetValidation();
        $this->reset('image', 'removeImage', 'notice', 'slideAssets', 'slideAlts', 'slideUploads', 'removeSlides', 'slidePaths', 'videoUploads', 'removeVideos');
        $this->sectionId = $section->id;
        $this->title = $section->title;
        $this->description = $section->description ?? '';
        $this->buttonText = $section->button_text ?? '';
        $this->buttonUrl = $section->button_url ?: '/catalogo';
        $this->imageAlt = $section->image_alt ?? '';
        $this->isActive = $section->is_active;
        $this->sortOrder = $section->sort_order;
        $this->type = $section->content['type'] ?? 'hero';
        $this->asset = $section->content['asset'] ?? '';
        $this->videoUrl = $section->content['video_url'] ?? '';
        $this->imageOnly = $section->content['image_only'] ?? true;
        $rawProductIds = array_map('intval', $section->content['product_ids'] ?? []);
        $validProductIds = Product::whereIn('id', $rawProductIds)->pluck('id')->all();
        $this->productIds = array_values(array_intersect($rawProductIds, $validProductIds));
        $this->highlightText = $section->content['highlight_text'] ?? '';
        $this->imagePosition = $section->content['image_position'] ?? 'left';

        $this->videoUrls = ['', '', ''];
        $this->videoTitles = ['', '', ''];
        $this->videoPaths = ['', '', ''];
        $this->removeVideos = [false, false, false];
        $rawVideos = $section->content['videos'] ?? [];
        if (empty($rawVideos) && ! empty($section->content['video_url'])) {
            $rawVideos = [['url' => $section->content['video_url'], 'title' => $section->title, 'path' => '']];
        }
        foreach (array_slice($rawVideos, 0, 3) as $i => $v) {
            $this->videoUrls[$i] = $v['url'] ?? '';
            $this->videoTitles[$i] = $v['title'] ?? '';
            $this->videoPaths[$i] = $v['path'] ?? '';
        }

        foreach ($section->slides() as $index => $slide) {
            $this->slideAssets[$index] = $slide['asset'] ?? '';
            $this->slideAlts[$index] = $slide['alt'] ?? '';
            $this->slidePaths[$index] = $slide['path'] ?? '';
            $this->slideUrls[$index] = $slide['url'] ?? '';
        }
    }

    public function create(): void
    {
        Gate::authorize('access-admin');
        $this->resetExcept('site');
        $this->title = 'Nueva sección';
        $this->type = 'banner';
        $this->highlightText = '';
        $this->imagePosition = 'left';
        $this->slideUrls = ['', '', '', ''];
        $this->productIds = [];
        $this->videoUrls = ['', '', ''];
        $this->videoTitles = ['', '', ''];
        $this->videoPaths = ['', '', ''];
        $this->removeVideos = [false, false, false];
        $this->videoUploads = [];
        $this->sortOrder = (int) Banner::max('sort_order') + 10;
    }

    public function delete(int $id): void
    {
        Gate::authorize('access-admin');
        $section = Banner::where('key', 'like', 'home-%')->findOrFail($id);

        if ($section->image_path) {
            Storage::disk('public')->delete($section->image_path);
        }

        foreach ($section->slides() as $slide) {
            if (! empty($slide['path'])) {
                Storage::disk('public')->delete($slide['path']);
            }
        }

        foreach ($section->content['videos'] ?? [] as $video) {
            if (! empty($video['path'])) {
                Storage::disk('public')->delete($video['path']);
            }
        }

        $section->delete();

        if ($this->sectionId === $id) {
            $this->create();
        }

        $this->notice = 'Sección eliminada correctamente.';
    }

    public function save(): void
    {
        Gate::authorize('access-admin');
        $this->validate([
            'title' => 'required|string|max:200', 'description' => 'nullable|string|max:2000',
            'buttonText' => 'nullable|string|max:100',
            'buttonUrl' => ['required', 'string', 'max:500', 'regex:#^/(?!/)[a-zA-Z0-9/_?=&%+,.~\-]*$#'],
            'imageAlt' => 'nullable|string|max:255',
            'asset' => ['nullable', Rule::in(array_keys($this->assets()))],
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:6144',
            'videoUrl' => ['nullable', 'url:https', 'max:1000', 'regex:/\.mp4(?:\?[^\s]*)?$/i'],
            'type' => ['required', Rule::in(['hero', 'banner', 'products', 'categories', 'lines', 'video', 'split', 'showcase'])],
            'highlightText' => 'nullable|string|max:500',
            'imagePosition' => ['nullable', Rule::in(['left', 'right'])],
            'isActive' => 'boolean', 'imageOnly' => 'boolean', 'removeImage' => 'boolean',
            'sortOrder' => 'integer|min:0|max:10000', 'productIds' => 'array|max:16',
            'productIds.*' => ['integer', 'distinct', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'slideAssets' => 'array|max:4',
            'slideAssets.*' => ['nullable', Rule::in(array_keys($this->assets()))],
            'slideAlts' => 'array|max:4', 'slideAlts.*' => 'nullable|string|max:255',
            'slideUrls' => 'array|max:4',
            'slideUrls.*' => ['nullable', 'string', 'max:500', 'regex:#^/(?!/)[a-zA-Z0-9/_?=&%+,.~\-]*$#'],
            'slideUploads' => 'array|max:4',
            'slideUploads.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:6144',
            'removeSlides' => 'array|max:4', 'removeSlides.*' => 'boolean',
            'videoUploads' => 'array|max:3',
            'videoUploads.*' => 'nullable|file|mimes:mp4,webm|max:40960',
            'videoUrls' => 'array|max:3',
            'videoUrls.*' => ['nullable', 'url:https', 'max:1000', 'regex:/\.(?:mp4|webm)(?:\?[^\s]*)?$/i'],
            'videoTitles' => 'array|max:3',
            'videoTitles.*' => 'nullable|string|max:200',
            'removeVideos' => 'array|max:3',
            'removeVideos.*' => 'boolean',
        ]);
        foreach (['slideAssets', 'slideAlts', 'slideUploads', 'removeSlides', 'slideUrls'] as $property) {
            if (array_diff(array_keys($this->{$property}), [0, 1, 2, 3])) {
                $this->addError($property, 'Solo se permiten cuatro posiciones de imagen.');

                return;
            }
        }
        foreach (['videoUrls', 'videoTitles', 'videoUploads', 'removeVideos'] as $property) {
            if (array_diff(array_keys($this->{$property}), [0, 1, 2])) {
                $this->addError($property, 'Solo se permiten hasta tres videos.');

                return;
            }
        }
        $section = $this->sectionId ? Banner::where('key', 'like', 'home-%')->findOrFail($this->sectionId) : new Banner(['key' => 'home-'.Str::uuid()]);
        $newImage = $this->image ? $this->image->store('banners', 'public') : ($this->removeImage ? null : $section->image_path);
        if ($newImage === false) {
            throw new \RuntimeException('No se pudo guardar la imagen.');
        }
        $uploadedPaths = [];
        try {
            $slides = [];
            if ($this->type === 'hero') {
                for ($index = 0; $index < 4; $index++) {
                    if ($this->removeSlides[$index] ?? false) {
                        continue;
                    }
                    $path = $this->slidePaths[$index] ?? '';
                    if ($this->slideUploads[$index] ?? null) {
                        $path = $this->slideUploads[$index]->store('banners', 'public');
                        if ($path === false) {
                            throw new \RuntimeException('No se pudo guardar la diapositiva.');
                        }
                        $uploadedPaths[] = $path;
                    } elseif (($this->slideAssets[$index] ?? '') !== ($section->slides()[$index]['asset'] ?? '')) {
                        $path = '';
                    }
                    $asset = $this->slideAssets[$index] ?? '';
                    if ($path || $asset) {
                        $slides[] = [
                            'path' => $path,
                            'asset' => $asset,
                            'alt' => $this->slideAlts[$index] ?: $this->title,
                            ...(! empty($this->slideUrls[$index]) ? ['url' => trim($this->slideUrls[$index])] : []),
                        ];
                    }
                }
            }

            $videos = [];
            if ($this->type === 'video') {
                for ($vIndex = 0; $vIndex < 3; $vIndex++) {
                    if ($this->removeVideos[$vIndex] ?? false) {
                        if (! empty($this->videoPaths[$vIndex])) {
                            Storage::disk('public')->delete($this->videoPaths[$vIndex]);
                        }

                        continue;
                    }

                    $path = $this->videoPaths[$vIndex] ?? '';
                    if ($this->videoUploads[$vIndex] ?? null) {
                        $path = $this->videoUploads[$vIndex]->store('videos', 'public');
                        if ($path === false) {
                            throw new \RuntimeException('No se pudo guardar el archivo de video.');
                        }
                        $uploadedPaths[] = $path;
                    }

                    $url = $this->videoUrls[$vIndex] ?? '';
                    $title = $this->videoTitles[$vIndex] ?? '';

                    if ($vIndex === 0 && ! $url && ! $path && $this->videoUrl) {
                        $url = $this->videoUrl;
                    }

                    if ($path || $url) {
                        $videos[] = [
                            'path' => $path,
                            'url' => $url,
                            'title' => $title ?: ($this->title.' · Video '.($vIndex + 1)),
                        ];
                    }
                }
            }

            $primaryVideoUrl = $this->videoUrl;
            if (empty($primaryVideoUrl) && ! empty($videos)) {
                $primaryVideoUrl = ! empty($videos[0]['url']) ? $videos[0]['url'] : ($videos[0]['path'] ? Storage::disk('public')->url($videos[0]['path']) : '');
            }

            $section->fill([
                'title' => trim($this->title), 'description' => trim($this->description),
                'button_text' => trim($this->buttonText), 'button_url' => $this->buttonUrl,
                'image_alt' => $this->imageAlt, 'image_path' => $newImage,
                'is_active' => $this->isActive, 'sort_order' => $this->sortOrder,
                'content' => [
                    'type' => $this->type,
                    'asset' => $this->asset,
                    'video_url' => $primaryVideoUrl,
                    'image_only' => $this->imageOnly,
                    'product_ids' => array_map('intval', $this->productIds),
                    'highlight_text' => trim($this->highlightText),
                    'image_position' => $this->imagePosition ?: 'left',
                    ...($this->type === 'hero' ? ['slides' => $slides] : []),
                    ...($this->type === 'video' ? ['videos' => $videos] : []),
                ],
            ])->save();
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($uploadedPaths);
            if ($this->image && $newImage) {
                Storage::disk('public')->delete($newImage);
            }
            throw $exception;
        }
        $this->edit($section->id);
        $this->notice = 'Sección guardada. Los cambios ya aparecen en la tienda.';
    }

    public function saveSite(): void
    {
        Gate::authorize('access-admin');
        $this->validate([
            'site.announcement' => 'nullable|string|max:200',
            'site.logo' => ['required', Rule::in(array_keys($this->assets()))],
            'site.footer_text' => 'nullable|string|max:1000', 'site.hours' => 'nullable|string|max:500',
            'site.instagram' => 'nullable|url:https|max:500', 'site.facebook' => 'nullable|url:https|max:500',
            'site.privacy' => 'nullable|url:https|max:500', 'site.terms' => 'nullable|url:https|max:500',
        ]);
        $settings = StoreSetting::firstOrCreate(['key' => 'main'], ['business_name' => 'VK Uniformes']);
        $settings->update(['storefront' => $this->site]);
        $this->notice = 'Cabecera y pie de página actualizados.';
    }

    /** @return array<string, string> */
    private function assets(): array
    {
        $assets = [];
        foreach (['slider', 'secciones', 'images', 'fotos'] as $folder) {
            foreach (glob(public_path($folder.'/*')) ?: [] as $path) {
                if (in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg', 'webp'], true)) {
                    $assets[$folder.'/'.basename($path)] = $folder.' / '.basename($path);
                }
            }
        }

        return $assets;
    }

    public function render(): View
    {
        return view('livewire.admin.landing.index', [
            'sections' => Banner::where('key', 'like', 'home-%')->orderBy('sort_order')->orderBy('id')->get(),
            'products' => Product::orderBy('name')->get(['id', 'name', 'is_active']),
            'assets' => $this->assets(),
            'selectedSection' => $this->sectionId ? Banner::find($this->sectionId) : null,
        ]);
    }
}
