@php
    $initialColor = $initialColor ?? '';
    $colors = $product->variants->where('is_active', true)->filter(fn ($v) => !empty($v->color_name))->unique('color_name');
    $cover = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
    $coverColor = $cover?->color_name;
    $cardColor = ($initialColor !== '' && $colors->contains('color_name', $initialColor))
        ? $initialColor
        : (($coverColor && $colors->contains('color_name', $coverColor)) ? $coverColor : '');
    $cardImages = $product->imagesForColor($cardColor);
    $cover = $cardImages->firstWhere('is_primary', true) ?? $cardImages->first() ?? $cover;
    $coverUrl = $cover ? Storage::disk('public')->url($cover->path) : '';
    $productUrl = route('store.product', $product->slug);
    $hasDiscount = $product->compare_price !== null && (float) $product->compare_price > (float) $product->price;
    $isSoldOut = (int) $product->available_stock === 0;
@endphp
<article class="showcase-card group flex flex-col justify-between rounded-2xl bg-white p-3 sm:p-4 transition-all duration-300 hover:shadow-lg"
    wire:key="showcase-card-{{ $product->id }}"
    x-data="{ color: @js($cardColor), photo: @js($coverUrl), alt: @js($cover?->alt_text ?: $product->name), url: @js($productUrl) }">
    
    <!-- FOTO DEL PRODUCTO -->
    <div class="relative overflow-hidden rounded-2xl bg-gray-50 aspect-[3/4] sm:aspect-[4/5] flex items-center justify-center">
        <a :href="url + (color ? '?color=' + encodeURIComponent(color) : '')" class="block h-full w-full">
            <img @if ($coverUrl) src="{{ $coverUrl }}" @endif :src="photo || null" x-show="photo" alt="{{ $cover?->alt_text ?: $product->name }}" :alt="alt" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" @if (!$coverUrl) style="display:none" @endif>
            <span class="flex h-full w-full items-center justify-center text-2xl font-bold text-gray-300" x-show="!photo" @if ($coverUrl) style="display:none" @endif>VK</span>
        </a>

        <!-- BADGES -->
        <div class="absolute left-2.5 top-2.5 flex flex-col gap-1 z-10">
            @if ($isSoldOut)
                <span class="rounded bg-black/85 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-widest text-white backdrop-blur-sm">
                    SOLD OUT
                </span>
            @elseif ($hasDiscount)
                <span class="rounded bg-[#a32233] px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-widest text-white shadow-sm">
                    OFERTA
                </span>
            @endif
        </div>
    </div>

    <!-- MUESTRAS DE COLOR (SWATCHES) -->
    <div class="pt-3 pb-1 flex flex-col items-center text-center">
        @if ($colors->isNotEmpty())
            <div class="flex flex-wrap items-center justify-center gap-1.5 mb-2" role="group" aria-label="Colores de {{ $product->name }}">
                @foreach ($colors as $variant)
                    @php
                        $colorImages = $product->imagesForColor($variant->color_name);
                        $colorImage = $colorImages->firstWhere('is_primary', true) ?? $colorImages->first();
                    @endphp
                    <button type="button" 
                        class="h-4 w-4 sm:h-5 sm:w-5 rounded-full transition-all duration-200 border border-black/15 shadow-sm hover:scale-110 focus:outline-none"
                        :class="color === '{{ addslashes($variant->color_name) }}' ? 'ring-2 ring-offset-1 ring-gray-900 scale-110' : 'opacity-85 hover:opacity-100'"
                        title="{{ $variant->color_name }}"
                        aria-label="{{ $variant->color_name }}"
                        data-color="{{ $variant->color_name }}"
                        data-photo="{{ $colorImage ? Storage::disk('public')->url($colorImage->path) : '' }}"
                        data-alt="{{ $colorImage?->alt_text ?: $product->name.' '.$variant->color_name }}"
                        x-on:click="color = $el.dataset.color; photo = $el.dataset.photo; alt = $el.dataset.alt"
                        style="background-color: {{ preg_match('/^#[0-9a-fA-F]{6}$/', $variant->color_hex ?? '') ? $variant->color_hex : '#dce1e7' }}">
                    </button>
                @endforeach
            </div>
        @else
            <div class="h-4 sm:h-5 mb-2"></div>
        @endif

        <!-- TÍTULO -->
        <h3 class="text-xs sm:text-sm font-semibold text-gray-900 line-clamp-1 hover:text-[#a32233] transition">
            <a :href="url + (color ? '?color=' + encodeURIComponent(color) : '')">
                {{ $product->name }}
            </a>
        </h3>

        <!-- CATEGORÍA / LÍNEA -->
        <p class="text-[11px] sm:text-xs text-gray-400 mt-0.5">
            {{ $product->category->name }}
        </p>

        <!-- PRECIO -->
        <p class="mt-1 text-sm sm:text-base font-bold text-gray-900">
            ${{ number_format((float) $product->price, 2) }}
            @if ($hasDiscount)
                <del class="text-xs font-normal text-gray-400 ml-1">${{ number_format((float) $product->compare_price, 2) }}</del>
            @endif
        </p>

        <!-- BOTÓN PILL DE ACCIÓN -->
        <div class="mt-3 w-full flex justify-center">
            @if ($isSoldOut)
                <a :href="url + (color ? '?color=' + encodeURIComponent(color) : '')" 
                   class="inline-flex w-full max-w-[200px] items-center justify-center rounded-full bg-[#8b1e2c] px-4 py-2 text-[11px] sm:text-xs font-bold uppercase tracking-wider text-white shadow-sm hover:bg-[#6e1823] transition-colors">
                    LEER MÁS
                </a>
            @else
                <a :href="url + (color ? '?color=' + encodeURIComponent(color) : '')" 
                   class="inline-flex w-full max-w-[200px] items-center justify-center rounded-full bg-[#a32233] px-4 py-2 text-[11px] sm:text-xs font-bold uppercase tracking-wider text-white shadow-sm hover:bg-[#851927] transition-colors">
                    SELECCIONAR OPCIONES
                </a>
            @endif
        </div>
    </div>
</article>
