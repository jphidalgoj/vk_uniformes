<div class="store-detail min-h-screen bg-white">
    <!-- =====================================
         CONTENEDOR PRINCIPAL
    ====================================== -->
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:py-10">

        <!-- NAVEGACIÓN -->
        <nav aria-label="Ruta de navegación" class="mb-8 flex flex-wrap items-center gap-2 text-xs text-[#716779]">
            <a href="{{ route('home') }}" class="hover:text-[#331344]">Inicio</a>
            <span>/</span>
            <a href="{{ route('store.catalog') }}" class="hover:text-[#331344]">Catálogo</a>
            <span>/</span>
            <a href="{{ route('store.category', $product->category->slug) }}" class="hover:text-[#331344]">
                {{ $product->category->name }}
            </a>
            <span>/</span>
            <span class="font-medium text-[#331344]">{{ $product->name }}</span>
        </nav>

        <!-- =====================================
             FOTOGRAFÍAS E INFORMACIÓN
        ====================================== -->
        <div class="grid gap-9 lg:grid-cols-2 lg:gap-14">

            <!-- GALERÍA DE FOTOGRAFÍAS -->
            <section class="min-w-0">
                <div class="relative aspect-[4/5] overflow-hidden rounded-3xl bg-[#F8F7FA] shadow-sm">
                    @if ($activeImage)
                        <img
                            wire:key="main-image-{{ $activeImage->id }}"
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($activeImage->path) }}"
                            alt="{{ $activeImage->alt_text ?: $product->name }}"
                            class="product-detail-photo h-full w-full object-cover transition duration-300"
                        >
                    @else
                        <div class="flex h-full items-center justify-center text-6xl font-bold text-[#B7A8C1]">
                            VK
                        </div>
                    @endif

                    <!-- ETIQUETAS -->
                    <div class="absolute left-4 top-4 flex flex-col gap-2 z-10">
                        @if (! $hasStock)
                            <span class="rounded-full bg-[#230C31] px-4 py-1.5 text-xs font-bold text-white shadow-sm">
                                Agotado
                            </span>
                        @elseif ($product->compare_price && $product->compare_price > $product->price)
                            <span class="rounded-full bg-[#331344] px-4 py-1.5 text-xs font-bold text-white shadow-sm">
                                Oferta
                            </span>
                        @elseif ($product->is_featured)
                            <span class="rounded-full bg-white px-4 py-1.5 text-xs font-bold text-[#331344] shadow-sm">
                                Destacado
                            </span>
                        @endif
                    </div>
                </div>

                <!-- MINIATURAS (HASTA 8 FOTOS) -->
                @if ($images->isNotEmpty())
                    <div class="mt-4 flex gap-3 overflow-x-auto pb-2">
                        @foreach ($images as $image)
                            <button
                                type="button"
                                wire:key="thumbnail-{{ $image->id }}"
                                wire:click="selectImage({{ $image->id }})"
                                aria-label="Ver fotografía {{ $loop->iteration }}"
                                aria-pressed="{{ $activeImage?->id === $image->id ? 'true' : 'false' }}"
                                class="h-24 w-20 shrink-0 overflow-hidden rounded-2xl border-2 transition {{ $activeImage?->id === $image->id ? 'border-[#331344] ring-2 ring-[#331344]/20 scale-105' : 'border-[#EAEBEE] hover:border-[#9998A6]' }}">
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                                    alt="{{ $image->alt_text ?: $product->name }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover"
                                >
                            </button>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- INFORMACIÓN DEL PRODUCTO -->
            <section class="min-w-0 lg:pt-2 space-y-6">

                <!-- CATEGORÍA -->
                <div>
                    <a href="{{ route('store.category', $product->category->slug) }}"
                       class="text-xs font-bold uppercase tracking-[0.2em] text-[#563369]">
                        {{ $product->category->name }}
                    </a>

                    <!-- NOMBRE -->
                    <h1 class="mt-2 text-3xl font-bold leading-tight tracking-tight text-[#230C31] sm:text-4xl">
                        {{ $product->name }}
                    </h1>

                    <!-- PRECIO -->
                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        <span class="text-3xl font-extrabold text-[#331344]">
                            ${{ number_format($product->price, 2) }}
                        </span>

                        @if ($product->compare_price && $product->compare_price > $product->price)
                            <span class="text-xl text-gray-400 line-through">
                                ${{ number_format($product->compare_price, 2) }}
                            </span>
                        @endif
                    </div>

                    <p class="mt-1 text-xs text-[#817689]">
                        Precio unitario en dólares estadounidenses.
                    </p>
                </div>

                <!-- DESCRIPCIÓN -->
                @if ($product->description)
                    <div class="border-t border-[#EAEBEE] pt-5">
                        <h2 class="text-sm font-bold text-[#230C31]">Descripción</h2>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-[#716779]">{{ $product->description }}</p>
                    </div>
                @endif

                <!-- =====================================
                     SELECCIÓN DE VARIANTES
                ====================================== -->
                <div class="space-y-6 border-t border-[#EAEBEE] pt-6">
                    <h2 class="text-base font-bold text-[#230C31]">
                        Configura tu uniforme
                    </h2>

                    <!-- COLOR (SOLO SI TIENE COLORES DEFINIDOS) -->
                    @if ($colors->isNotEmpty())
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-semibold text-[#230C31]">
                                    Color: <span class="font-bold text-[#563369]">{{ $selectedColor ?: 'Selecciona un color' }}</span>
                                </p>
                                <span wire:loading wire:target="selectColor,selectedColor" class="text-xs font-semibold text-purple-600 animate-pulse">
                                    Cambiando color…
                                </span>
                            </div>

                            <div class="product-color-options" role="group" aria-label="Selecciona un color">
                                @foreach ($variants->filter(fn ($v) => !empty($v->color_name))->unique('color_name') as $variant)
                                    <button type="button" 
                                        wire:key="color-btn-{{ $variant->id }}"
                                        wire:click="selectColor({{ Illuminate\Support\Js::from($variant->color_name) }})"
                                        aria-pressed="{{ $selectedColor === $variant->color_name ? 'true' : 'false' }}"
                                        class="product-color-option transition hover:border-[#331344]">
                                        <span class="color-dot" style="--swatch: {{ preg_match('/^#[0-9a-fA-F]{6}$/', $variant->color_hex ?? '') ? $variant->color_hex : '#dce1e7' }}"></span>
                                        <span class="font-medium">{{ $variant->color_name }}</span>
                                    </button>
                                @endforeach
                            </div>

                            @if ($selectedColor && !$images->contains('color_name', $selectedColor))
                                <p class="mt-2 text-xs text-gray-500 italic">
                                    {{ $images->isEmpty() ? 'Todavía no hay fotografías de este color.' : 'Fotografías de referencia. Este color aún no tiene fotos propias.' }}
                                </p>
                            @endif
                        </div>
                    @else
                        <!-- ESTILO PERSONALIZADO SIN COLOR -->
                        <div class="rounded-2xl border border-purple-100 bg-purple-50/50 p-4">
                            <p class="text-xs font-semibold text-[#331344]">
                                ✦ Prenda con estilo personalizado
                            </p>
                            <p class="mt-1 text-xs text-gray-600">
                                Este modelo se elabora bajo diseño exclusivo. Selecciona tu talla abajo para cotizar o consultar disponibilidad directamente.
                            </p>
                        </div>
                    @endif

                    <!-- SELECCIÓN DE TALLA (DISEÑO TILES COMO EN IMAGEN 4) -->
                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <span class="text-sm font-bold text-[#230C31]">
                                Seleccionar una talla
                            </span>
                            <span class="text-xs font-semibold text-[#563369] underline cursor-pointer hover:text-[#230C31]">
                                Tabla de tamaños
                            </span>
                        </div>

                        @if ($sizes->isNotEmpty())
                            <div class="grid grid-cols-4 gap-2 sm:grid-cols-6 md:grid-cols-8" role="radiogroup" aria-label="Tallas disponibles">
                                @foreach ($sizes as $sizeItem)
                                    @php
                                        $sizeObj = (object) $sizeItem;
                                        $sizeName = $sizeObj->size;
                                        $isSelected = $selectedSize === $sizeName;
                                        $isOutOfStock = (int) $sizeObj->stock === 0;
                                    @endphp
                                    <button
                                        type="button"
                                        wire:key="size-tile-{{ $sizeObj->id }}-{{ $sizeName }}"
                                        wire:click="selectSize('{{ $sizeName }}')"
                                        role="radio"
                                        aria-checked="{{ $isSelected ? 'true' : 'false' }}"
                                        title="Talla {{ $sizeName }}{{ $isOutOfStock ? ' (Sin stock en este momento)' : ' (Disponible)' }}"
                                        class="group relative flex h-12 items-center justify-center rounded-xl border text-sm font-bold transition-all duration-200
                                            {{ $isSelected 
                                                ? 'border-2 border-[#230C31] bg-white text-[#230C31] shadow-sm ring-1 ring-[#230C31]' 
                                                : ($isOutOfStock 
                                                    ? 'border-gray-200 bg-gray-50/90 text-gray-400 hover:border-gray-300' 
                                                    : 'border-gray-300 bg-white text-gray-800 hover:border-[#230C31] hover:text-[#230C31]') }}">
                                        
                                        <span>{{ $sizeName }}</span>

                                        @if ($isOutOfStock)
                                            <!-- LÍNEA DIAGONAL TACHADO CUANDO NO HAY STOCK (COMO EN IMAGEN 4) -->
                                            <svg class="pointer-events-none absolute inset-0 h-full w-full stroke-gray-300/80" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none">
                                                <line x1="0" y1="100" x2="100" y2="0" stroke-width="1.5" />
                                            </svg>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                            <p class="mt-2.5 text-xs text-gray-500">
                                Ajuste de tamaño profesional. Confeccionado para durabilidad y máxima comodidad en tu jornada laboral.
                            </p>
                        @else
                            <p class="text-xs text-gray-500 italic">No hay tallas disponibles en este momento.</p>
                        @endif
                    </div>

                    <!-- ESTADO DE DISPONIBILIDAD -->
                    <div class="rounded-2xl border border-[#E3DCE7] bg-[#F8F7FA] p-4" aria-live="polite">
                        @if (! $hasStock)
                            <p class="text-sm font-bold text-red-700">Producto agotado</p>
                            <p class="mt-1 text-xs text-[#716779]">Actualmente no hay unidades disponibles en inventario.</p>
                        @elseif ($selectedVariant)
                            @if ($selectedVariant->stock > 0)
                                <p class="text-sm font-bold text-green-700">✓ Disponible para entrega o confección</p>
                                <p class="mt-1 text-xs text-[#716779]">
                                    Existencias: {{ $selectedVariant->stock }} unidades en talla {{ $selectedVariant->size }}
                                    {{ $selectedVariant->color_name ? 'y color '.$selectedVariant->color_name : '' }}.
                                </p>
                            @else
                                <p class="text-sm font-bold text-amber-700">Talla agotada en este momento</p>
                                <p class="mt-1 text-xs text-[#716779]">Puedes consultar por WhatsApp para solicitar confección personalizada.</p>
                            @endif
                        @else
                            <p class="text-sm font-medium text-[#331344]">
                                {{ $colors->isEmpty() ? 'Selecciona una talla para continuar' : ($selectedColor === '' ? 'Selecciona color y talla para consultar' : 'Selecciona una talla disponible') }}
                            </p>
                            <p class="mt-1 text-xs text-[#716779]">
                                Te mostraremos la disponibilidad exacta para cotizar de inmediato.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- =====================================
                     SOLICITUDES Y CONSULTAS POR WHATSAPP
                ====================================== -->
                <section class="space-y-4 border-t border-[#EAEBEE] pt-6">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.15em] text-[#563369]">
                            Atención directa VK Uniformes
                        </span>
                        <h2 class="mt-1 text-xl font-bold text-[#230C31]">
                            ¿Te interesa este uniforme?
                        </h2>
                    </div>

                    @if ($whatsappEnabled)
                        <!-- SOLICITAR COTIZACIÓN -->
                        @if ($quotationUrl)
                            <a
                                href="{{ $quotationUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="flex min-h-14 w-full items-center justify-center gap-3 rounded-xl bg-[#331344] px-5 text-center text-sm font-bold text-white shadow-md transition hover:bg-[#563369]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9 L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5 a8.48 8.48 0 0 1 8 8z"/>
                                </svg>
                                Solicitar cotización por WhatsApp
                            </a>
                        @else
                            <button
                                type="button"
                                onclick="document.querySelector('[role=radiogroup]')?.scrollIntoView({behavior: 'smooth'})"
                                class="flex min-h-14 w-full items-center justify-center rounded-xl bg-[#525252] px-5 text-center text-sm font-bold tracking-wider uppercase text-white shadow-sm transition hover:bg-[#3f3f3f]">
                                SELECCIONAR UN TAMAÑO
                            </button>
                        @endif

                        <!-- CONSULTAR MÁS INFORMACIÓN (SIEMPRE DISPONIBLE) -->
                        <a
                            href="{{ $informationUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl border-2 border-[#331344] bg-white px-5 text-center text-sm font-bold text-[#331344] transition hover:bg-[#F8F7FA]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            Consultar información general por WhatsApp
                        </a>

                        <p class="text-center text-xs text-[#817689]">
                            Se abrirá WhatsApp con el producto y datos de tu selección listos para enviar.
                        </p>
                    @endif
                </section>

                <a href="{{ route('store.catalog') }}"
                   class="mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-gray-200 px-6 text-xs font-semibold text-[#716779] transition hover:bg-gray-50 hover:text-[#230C31]">
                    ← Volver al catálogo completo
                </a>
            </section>
        </div>
    </main>
</div>
