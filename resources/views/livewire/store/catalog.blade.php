
<div class="store-catalog min-h-screen bg-white">

    {{-- ============================================================
         BANDA DE CATEGORÍAS (SCROLLABLE HORIZONTAL EN TODOS LOS TAMAÑOS)
    ============================================================ --}}
    <div class="border-b border-gray-100 bg-white">
        <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
            <nav aria-label="Categorías"
                 class="no-scrollbar flex gap-2 overflow-x-auto py-3">

                <a href="{{ route('store.catalog') }}"
                   class="shrink-0 whitespace-nowrap rounded-full border px-5 py-2 text-xs font-semibold transition
                          {{ ! $currentCategory
                             ? 'border-[#331344] bg-[#331344] text-white'
                             : 'border-gray-200 bg-white text-[#331344] hover:border-[#331344]' }}">
                    Todos
                </a>

                @foreach ($categories as $category)
                    <a href="{{ route('store.category', $category->slug) }}"
                       wire:key="cat-pill-{{ $category->id }}"
                       class="shrink-0 whitespace-nowrap rounded-full border px-5 py-2 text-xs font-semibold transition
                              {{ $currentCategory?->id === $category->id
                                 ? 'border-[#331344] bg-[#331344] text-white'
                                 : 'border-gray-200 bg-white text-[#331344] hover:border-[#331344]' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- ============================================================
         BARRA DE FILTROS HORIZONTAL (ESTILO FIGS)
    ============================================================ --}}
    <div class="sticky top-[var(--header-h,72px)] z-30 border-b border-gray-100 bg-white/95 backdrop-blur-sm">
        <div class="mx-auto max-w-screen-xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-2 py-2.5 sm:gap-3">

                {{-- Buscar --}}
                <div class="relative min-w-[160px] flex-1 sm:max-w-xs">
                    <label for="catalog-search" class="sr-only">Buscar</label>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M16.65 16.65A7 7 0 1 0 2 9a7 7 0 0 0 14.65 7.65z"/>
                    </svg>
                    <input id="catalog-search"
                           type="search"
                           wire:model.live.debounce.350ms="search"
                           placeholder="Buscar uniformes..."
                           class="h-9 w-full rounded-full border border-gray-200 bg-gray-50 pl-9 pr-4 text-sm text-gray-800 outline-none focus:border-[#331344] focus:bg-white">
                </div>

                {{-- Color --}}
                <div class="relative">
                    <label for="filter-color" class="sr-only">Color</label>
                    <select id="filter-color"
                            wire:model.live="color"
                            class="h-9 cursor-pointer appearance-none rounded-full border border-gray-200 bg-white pl-4 pr-8 text-sm font-medium text-gray-700 outline-none focus:border-[#331344]
                                   {{ $color !== '' ? 'border-[#331344] text-[#331344] font-semibold' : '' }}">
                        <option value="">COLOR</option>
                        @foreach ($availableColors as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-gray-500"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Talla --}}
                <div class="relative">
                    <label for="filter-size" class="sr-only">Talla</label>
                    <select id="filter-size"
                            wire:model.live="size"
                            class="h-9 cursor-pointer appearance-none rounded-full border border-gray-200 bg-white pl-4 pr-8 text-sm font-medium text-gray-700 outline-none focus:border-[#331344]
                                   {{ $size !== '' ? 'border-[#331344] text-[#331344] font-semibold' : '' }}">
                        <option value="">TALLA</option>
                        @foreach ($availableSizes as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-gray-500"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Ordenar --}}
                <div class="relative">
                    <label for="filter-sort" class="sr-only">Ordenar</label>
                    <select id="filter-sort"
                            wire:model.live="sort"
                            class="h-9 cursor-pointer appearance-none rounded-full border border-gray-200 bg-white pl-4 pr-8 text-sm font-medium text-gray-700 outline-none focus:border-[#331344]">
                        <option value="recientes">RECIENTES</option>
                        <option value="precio_asc">MENOR PRECIO</option>
                        <option value="precio_desc">MAYOR PRECIO</option>
                        <option value="nombre">NOMBRE A-Z</option>
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-gray-500"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Solo disponibles --}}
                <label class="flex h-9 cursor-pointer items-center gap-2 rounded-full border border-gray-200 bg-white px-4 text-sm font-medium text-gray-700
                              {{ $onlyInStock ? 'border-[#331344] text-[#331344] font-semibold' : '' }}">
                    <input type="checkbox" wire:model.live="onlyInStock"
                           class="h-3.5 w-3.5 accent-[#331344]">
                    Disponibles
                </label>

                {{-- Limpiar filtros --}}
                @if ($activeFilters > 0)
                    <button type="button"
                            wire:click="clearFilters"
                            class="flex h-9 items-center gap-1.5 rounded-full border border-red-200 bg-red-50 px-4 text-sm font-semibold text-red-600 transition hover:bg-red-100">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Limpiar ({{ $activeFilters }})
                    </button>
                @endif

                {{-- Total a la derecha --}}
                <span class="ml-auto hidden text-sm text-gray-500 sm:block"
                      wire:loading.class="opacity-50"
                      wire:target="search,sort,size,color,onlyInStock">
                    <span class="font-semibold text-gray-800">{{ $products->total() }}</span>
                    {{ $products->total() === 1 ? 'producto' : 'productos' }}
                </span>
            </div>
        </div>
    </div>

    {{-- ============================================================
         TÍTULO DE COLECCIÓN
    ============================================================ --}}
    <div class="mx-auto max-w-screen-xl px-4 pb-4 pt-8 sm:px-6 lg:px-8">
        <p class="text-xs font-bold uppercase tracking-[.22em] text-[#563369]">
            VK Uniformes · Colecciones
        </p>

        <h1 class="mt-2 text-3xl font-bold uppercase tracking-tight text-gray-900 sm:text-4xl">
            {{ $currentCategory ? $currentCategory->name : 'Nuestros uniformes' }}
        </h1>

        @if ($currentCategory && $currentCategory->description)
            <p class="mt-2 text-sm leading-6 text-gray-500">
                {{ $currentCategory->description }}
            </p>
        @endif

        {{-- Total visible en móvil --}}
        <p class="mt-3 text-sm text-gray-500 sm:hidden">
            <span class="font-semibold text-gray-800">{{ $products->total() }}</span>
            {{ $products->total() === 1 ? 'producto' : 'productos' }}
        </p>
    </div>

    {{-- ============================================================
         CUADRÍCULA DE PRODUCTOS — 2 col móvil · 3 col desktop
    ============================================================ --}}
    <div class="mx-auto max-w-screen-xl px-4 pb-16 sm:px-6 lg:px-8">

        <div wire:loading.class="opacity-60"
             wire:target="search,sort,size,color,onlyInStock"
             class="grid grid-cols-2 gap-x-4 gap-y-10 transition-opacity sm:gap-x-6 md:grid-cols-3 lg:gap-x-8 lg:gap-y-14">

            @forelse ($products as $product)
                @include('livewire.store.partials.home-product-card', ['initialColor' => $color])
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-6 py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">No encontramos productos</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Prueba otra búsqueda o explora nuestras categorías.
                    </p>
                    <a href="{{ route('store.catalog') }}"
                       class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-[#331344] px-6 text-sm font-semibold text-white transition hover:bg-[#230C31]">
                        Ver todo el catálogo
                    </a>
                </div>
            @endforelse
        </div>

        {{-- PAGINACIÓN --}}
        @if ($products->hasPages())
            <div class="mt-12 border-t border-gray-100 pt-8">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
