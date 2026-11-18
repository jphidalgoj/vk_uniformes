@php
    $initialColor = $initialColor ?? '';
    $colors = $product->variants->where('is_active', true)->filter(fn ($v) => !empty($v->color_name))->unique('color_name');
    $cover = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
    $coverColor = $cover?->color_name;
    $cardColor = ($initialColor !== '' && $colors->contains('color_name', $initialColor))
        ? $initialColor
        : (($coverColor && $colors->contains('color_name', $coverColor)) ? $coverColor : ($colors->first()?->color_name ?? ''));
    $cardImages = $product->imagesForColor($cardColor);
    $cover = $cardImages->firstWhere('is_primary', true) ?? $cardImages->first() ?? $cover;
    $coverUrl = $cover ? Storage::disk('public')->url($cover->path) : '';
    $productUrl = route('store.product', $product->slug);
    $hasDiscount = $product->compare_price !== null && (float) $product->compare_price > (float) $product->price;
    $isSoldOut = (int) $product->available_stock === 0;
    $visibleColors = $colors->take(5);
    $extraColorsCount = max(0, $colors->count() - 5);
@endphp
<article class="figs-product-card group flex flex-col justify-between w-full transition-all duration-300"
    wire:key="product-card-{{ $product->id }}-{{ md5($initialColor) }}"
    x-data="{ color: @js($cardColor), photo: @js($coverUrl), alt: @js($cover?->alt_text ?: $product->name), url: @js($productUrl) }">
    
    <!-- FOTO DEL PRODUCTO (FIGS TALL PORTRAIT, BORDERLESS) -->
    <div class="relative overflow-hidden rounded-2xl bg-[#f5f6f8] aspect-[3/4] w-full flex items-center justify-center">
        <a :href="url + (color ? '?color=' + encodeURIComponent(color) : '')" class="block h-full w-full">
            <img @if ($coverUrl) src="{{ $coverUrl }}" @endif :src="photo || null" x-show="photo" alt="{{ $cover?->alt_text ?: $product->name }}" :alt="alt" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" @if (!$coverUrl) style="display:none" @endif>
            <span class="flex h-full w-full items-center justify-center text-3xl font-bold text-gray-300" x-show="!photo" @if ($coverUrl) style="display:none" @endif>VK</span>
        </a>

        <!-- BADGES SUPERIOR IZQUIERDA -->
        <div class="absolute left-3 top-3 flex flex-col gap-1.5 z-10 pointer-events-none">
            @if ($isSoldOut)
                <span class="rounded bg-black/85 px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-widest text-white backdrop-blur-sm">
                    Agotado
                </span>
            @elseif ($hasDiscount)
                <span class="rounded bg-[#8b1e2c] px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-widest text-white shadow-sm">
                    Oferta
                </span>
            @endif
        </div>

        <!-- BOTÓN WISHLIST CORAZÓN (FIGS STYLE EN ESQUINA SUPERIOR DERECHA) -->
        <button type="button" aria-label="Favorito" class="absolute right-3 top-3 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-white/70 text-gray-800 backdrop-blur-sm transition-transform hover:scale-110 hover:bg-white hover:text-red-600">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </button>
    </div>

    <!-- INFORMACIÓN DE PRODUCTO Y COLORES (FIGS STYLE) -->
    <div class="pt-3 flex flex-col flex-grow justify-between">
        <div>
            <!-- MUESTRAS DE COLOR INTERACTIVAS (CON CHECKMARK EN COLOR ACTIVO) -->
            @if ($colors->isNotEmpty())
                <div class="flex items-center gap-1.5 flex-wrap mb-1" role="group" aria-label="Colores de {{ $product->name }}">
                    @foreach ($visibleColors as $variant)
                        @php
                            $colorImages = $product->imagesForColor($variant->color_name);
                            $colorImage = $colorImages->firstWhere('is_primary', true) ?? $colorImages->first();
                            $hex = preg_match('/^#[0-9a-fA-F]{6}$/', $variant->color_hex ?? '') ? $variant->color_hex : '#dce1e7';
                        @endphp
                        <button type="button"
                            class="relative flex h-5 w-5 sm:h-5 sm:w-5 items-center justify-center rounded-full border border-black/20 transition-transform hover:scale-110 focus:outline-none"
                            :class="color === '{{ addslashes($variant->color_name) }}' ? 'ring-2 ring-offset-1 ring-gray-900 scale-105' : 'opacity-85 hover:opacity-100'"
                            title="{{ $variant->color_name }}"
                            aria-label="{{ $variant->color_name }}"
                            data-color="{{ $variant->color_name }}"
                            data-photo="{{ $colorImage ? Storage::disk('public')->url($colorImage->path) : '' }}"
                            data-alt="{{ $colorImage?->alt_text ?: $product->name.' '.$variant->color_name }}"
                            x-on:click="color = $el.dataset.color; photo = $el.dataset.photo; alt = $el.dataset.alt"
                            style="background-color: {{ $hex }}">
                            
                            <!-- CHECKMARK EN COLOR ACTIVO (FIGS STYLE) -->
                            <svg x-show="color === '{{ addslashes($variant->color_name) }}'" class="h-3 w-3 {{ in_array(strtolower($hex), ['#ffffff', '#fff', '#f8f9fa', '#f5f5f5', '#e9ecef']) ? 'text-gray-900' : 'text-white' }} drop-shadow-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    @endforeach

                    @if ($extraColorsCount > 0)
                        <span class="text-[11px] font-semibold text-gray-500 pl-0.5">
                            +{{ $extraColorsCount }}
                        </span>
                    @endif
                </div>

                <!-- NOMBRE DEL COLOR ACTIVO (COMO 'Musgo' O 'Negro' EN LA IMAGEN) -->
                <p class="text-xs font-normal text-gray-500 capitalize" x-text="color || 'Color único'">
                    {{ $cardColor ?: 'Color único' }}
                </p>
            @else
                <p class="text-xs font-normal text-gray-400">
                    Estilo exclusivo
                </p>
            @endif

            <!-- TÍTULO DEL PRODUCTO -->
            <h3 class="mt-1 text-sm font-semibold text-gray-900 line-clamp-2 leading-snug hover:text-[#563369] transition">
                <a :href="url + (color ? '?color=' + encodeURIComponent(color) : '')">
                    {{ $product->name }}
                </a>
            </h3>
        </div>

        <!-- PRECIO Y BADGE INFERIOR -->
        <div class="mt-1.5">
            <p class="text-sm font-bold text-gray-900">
                ${{ number_format((float) $product->price, 2) }}
                @if ($hasDiscount)
                    <del class="text-xs font-normal text-gray-400 ml-1.5">${{ number_format((float) $product->compare_price, 2) }}</del>
                @endif
            </p>

            <div class="mt-1 flex items-center gap-1.5">
                <span class="text-[11px] font-semibold text-[#1e3a5f]">
                    {{ $product->category->name }}
                </span>
                <span class="text-gray-300">·</span>
                <span class="text-[11px] font-semibold text-[#2563eb]">
                    Los Más Vendidos
                </span>
            </div>
        </div>
    </div>
</article>
