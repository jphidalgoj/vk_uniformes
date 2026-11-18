
<div class="space-y-7">

    <!-- ========================================
         ENCABEZADO
    ========================================= -->
    <section class="flex flex-col gap-4
                    sm:flex-row sm:items-center
                    sm:justify-between">

        <div>
            <span class="text-xs font-semibold uppercase
                         tracking-widest text-[#563369]">
                Administración / Catálogo
            </span>

            <h1 class="mt-2 text-3xl font-semibold
                       tracking-tight text-[#230C31]">
                Productos
            </h1>

            <p class="mt-2 text-sm text-[#716779]">
                Administra los uniformes, precios
                y existencias de VK Uniformes.
            </p>
        </div>

        <button
            type="button"
            wire:click="create"
            class="inline-flex min-h-12 items-center
                   justify-center gap-2 rounded-xl
                   bg-[#331344] px-6 text-sm
                   font-semibold text-white
                   transition hover:bg-[#563369]">

            <span class="text-xl">+</span>
            Nuevo producto
        </button>
    </section>

    <!-- ========================================
         NOTIFICACIONES
    ========================================= -->
    @if ($notice)
        <div role="status"
             class="flex items-center justify-between
                    gap-3 rounded-xl border
                    border-[#DED0E6] bg-[#F5EEF8]
                    px-4 py-3 text-sm text-[#331344]">

            <span>{{ $notice }}</span>

            <button
                type="button"
                wire:click="$set('notice', '')"
                aria-label="Cerrar notificación"
                class="text-lg font-bold">
                &times;
            </button>
        </div>
    @endif

    <!-- ========================================
         INDICADORES
    ========================================= -->
    <section class="grid grid-cols-2 gap-3 sm:gap-5">

        <article class="rounded-2xl border border-[#EAEBEE]
                        bg-white p-5 shadow-sm">

            <p class="text-sm text-[#716779]">
                Productos encontrados
            </p>

            <p class="mt-3 text-3xl font-bold text-[#331344]">
                {{ $products->total() }}
            </p>
        </article>

        <article class="rounded-2xl border border-[#EAEBEE]
                        bg-white p-5 shadow-sm">

            <p class="text-sm text-[#716779]">
                Categorías disponibles
            </p>

            <p class="mt-3 text-3xl font-bold text-[#331344]">
                {{ $categories->count() }}
            </p>
        </article>
    </section>

    <!-- ========================================
         BUSCADOR Y FILTROS
    ========================================= -->
    <section class="rounded-2xl border border-[#EAEBEE]
                    bg-white p-4 shadow-sm sm:p-5">

        <div class="grid gap-3 sm:grid-cols-[1fr_190px]">

            <div>
                <label for="product-search" class="sr-only">
                    Buscar productos
                </label>

                <input
                    id="product-search"
                    type="search"
                    wire:model.live.debounce.350ms="search"
                    placeholder="Buscar productos..."
                    class="min-h-12 w-full rounded-xl
                           border border-[#E3DCE7]
                           bg-[#F8F7FA] px-4 text-sm
                           text-[#230C31] outline-none
                           focus:border-[#563369]"
                >
            </div>

            <div>
                <label for="product-status" class="sr-only">
                    Estado del producto
                </label>

                <select
                    id="product-status"
                    wire:model.live="status"
                    class="min-h-12 w-full rounded-xl
                           border border-[#E3DCE7]
                           bg-white px-4 text-sm
                           text-[#331344] outline-none
                           focus:border-[#563369]">

                    <option value="all">Todos</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                    <option value="archived">Archivados</option>
                </select>
            </div>
        </div>
    </section>

    <!-- ========================================
         LISTADO DE PRODUCTOS
    ========================================= -->
    <section class="space-y-3">

        @forelse ($products as $product)

            <article
                wire:key="product-{{ $product->id }}"
                class="rounded-2xl border border-[#EAEBEE]
                       bg-white p-4 shadow-sm
                       transition hover:shadow-md sm:p-5">

                <div class="flex items-start gap-4">

                    
                <!-- FOTOGRAFÍA PRINCIPAL -->
                @php
                    $cover = $product->images->firstWhere('is_primary', true)
                        ?? $product->images->first();
                @endphp

                <div class="h-22 w-20 shrink-0
                            overflow-hidden rounded-xl
                            bg-[#F1EBF4]">

                    @if ($cover)

                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cover->path) }}"
                            alt="{{ $cover->alt_text ?? $product->name }}"
                            loading="lazy"
                            class="h-full w-full object-cover"
                        >

                    @else

                        <div class="flex h-full w-full
                                    items-center justify-center
                                    text-lg font-bold text-[#A99AB4]">
                            VK
                        </div>

                    @endif
                </div>


                    <!-- INFORMACIÓN -->
                    <div class="min-w-0 flex-1">

                        <div class="flex flex-wrap items-start
                                    justify-between gap-2">

                            <div>
                                <h2 class="text-base font-semibold
                                           text-[#230C31]">
                                    {{ $product->name }}
                                </h2>

                                <p class="mt-1 text-xs
                                          text-[#817689]">
                                    {{ $product->category->name }}
                                </p>
                            </div>

                            @if ($product->trashed())

                                <span class="rounded-full
                                             bg-slate-100 px-3 py-1
                                             text-xs font-semibold
                                             text-slate-600">
                                    Archivado
                                </span>

                            @elseif ($product->is_active)

                                <span class="rounded-full
                                             bg-[#F1E9F5] px-3 py-1
                                             text-xs font-semibold
                                             text-[#331344]">
                                    Activo
                                </span>

                            @else

                                <span class="rounded-full
                                             bg-slate-100 px-3 py-1
                                             text-xs font-semibold
                                             text-slate-500">
                                    Inactivo
                                </span>

                            @endif
                        </div>

                        <div class="mt-4 flex flex-wrap
                                    items-center gap-4">

                            <span class="text-lg font-bold
                                         text-[#331344]">
                                ${{ number_format($product->price, 2) }}
                            </span>

                            @if ($product->compare_price)
                                <span class="text-sm
                                             text-[#8B8191] line-through">
                                    ${{ number_format($product->compare_price, 2) }}
                                </span>
                            @endif

                            <span class="rounded-lg
                                         bg-[#F8F7FA] px-3 py-1.5
                                         text-xs font-medium
                                         text-[#716779]">
                                Stock:
                                {{ $product->variants_sum_stock ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ACCIONES -->
                <div class="mt-5 flex flex-wrap
                            justify-end gap-2
                            border-t border-[#F0ECF2] pt-4">

                    @if ($product->trashed())

                        <button
                            type="button"
                            wire:click="restore({{ $product->id }})"
                            wire:confirm="¿Restaurar este producto?"
                            class="min-h-10 rounded-xl
                                   bg-[#331344] px-5
                                   text-sm font-semibold text-white
                                   transition hover:bg-[#563369]">

                            Restaurar
                        </button>

                    @else

                        
                        <!-- GESTIONAR FOTOGRAFÍAS -->
                        <a
                            href="{{ route('admin.products.images', $product->id) }}"
                            class="inline-flex min-h-10 items-center
                                justify-center gap-2 rounded-xl
                                bg-[#331344] px-5 text-sm
                                font-semibold text-white
                                transition hover:bg-[#563369]">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                width="17" height="17"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <rect x="3" y="3"
                                    width="18" height="18" rx="2"/>

                                <circle cx="8.5" cy="8.5" r="1.5"/>

                                <path d="m21 15-5-5L5 21"/>
                            </svg>

                            Fotografías
                        </a>


                        <button
                            type="button"
                            wire:click="edit({{ $product->id }})"
                            class="min-h-10 rounded-xl
                                   border border-[#E3DCE7]
                                   px-5 text-sm font-medium
                                   text-[#331344]
                                   transition hover:bg-[#F8F7FA]">

                            Editar
                        </button>

                        <button
                            type="button"
                            wire:click="archive({{ $product->id }})"
                            wire:confirm="¿Archivar este producto? Dejará de aparecer en el catálogo."
                            class="min-h-10 rounded-xl
                                   border border-red-100 px-5
                                   text-sm font-medium text-red-600
                                   transition hover:bg-red-50">

                            Archivar
                        </button>

                    @endif
                </div>
            </article>

        @empty

            <div class="rounded-2xl border
                        border-dashed border-[#D9CDDF]
                        bg-white px-6 py-16 text-center">

                <div class="mx-auto flex h-16 w-16
                            items-center justify-center
                            rounded-2xl bg-[#F1EBF4]
                            text-2xl font-bold text-[#331344]">
                    VK
                </div>

                <h3 class="mt-5 text-lg font-semibold
                           text-[#331344]">
                    No se encontraron productos
                </h3>

                <p class="mt-2 text-sm text-[#716779]">
                    Puedes modificar los filtros
                    o registrar un nuevo uniforme.
                </p>
            </div>

        @endforelse
    </section>

    <!-- PAGINACIÓN -->
    <div>
        {{ $products->links() }}
    </div>

    <!-- ========================================
         FORMULARIO DE PRODUCTO
    ========================================= -->
    @if ($showForm)

        <div class="fixed inset-0 z-[100]
                    overflow-y-auto
                    bg-[#16081F]/75 p-3
                    backdrop-blur-sm sm:p-6">

            <div role="dialog"
                 aria-modal="true"
                 aria-labelledby="product-form-title"
                 class="mx-auto my-4 w-full max-w-4xl
                        overflow-hidden rounded-2xl
                        bg-white shadow-2xl">

                <!-- ENCABEZADO -->
                <div class="flex items-center
                            justify-between gap-4
                            border-b border-[#EAEBEE]
                            px-5 py-5 sm:px-7">

                    <div>
                        <span class="text-xs font-semibold
                                     uppercase tracking-widest
                                     text-[#563369]">
                            Catálogo VK Uniformes
                        </span>

                        <h2 id="product-form-title"
                            class="mt-2 text-xl font-semibold
                                   text-[#230C31]">

                            {{ $editingId
                                ? 'Editar producto'
                                : 'Nuevo producto' }}
                        </h2>
                    </div>

                    <button
                        type="button"
                        wire:click="cancelForm"
                        aria-label="Cerrar formulario"
                        class="flex h-10 w-10 shrink-0
                               items-center justify-center
                               rounded-xl bg-[#F8F7FA]
                               text-2xl text-[#331344]">

                        &times;
                    </button>
                </div>

                <!-- FORMULARIO -->
                <form wire:submit="save"
                      class="space-y-8 p-5 sm:p-7">

                    <!-- INFORMACIÓN GENERAL -->
                    <section class="space-y-5">

                        <h3 class="border-b border-[#EAEBEE]
                                   pb-3 text-base font-semibold
                                   text-[#331344]">
                            01. Información del producto
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2">

                            <!-- NOMBRE -->
                            <div>
                                <label for="product-name"
                                       class="mb-2 block text-sm
                                              font-medium text-[#331344]">
                                    Nombre del producto *
                                </label>

                                <input
                                    id="product-name"
                                    type="text"
                                    wire:model="name"
                                    maxlength="200"
                                    required
                                    placeholder="Ej. Scrub Victoria"
                                    class="min-h-12 w-full rounded-xl
                                           border border-[#E3DCE7]
                                           bg-[#F8F7FA] px-4
                                           text-sm outline-none
                                           focus:border-[#563369]"
                                >

                                @error('name')
                                    <p class="mt-2 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- CATEGORÍA -->
                            <div>
                                <label for="product-category"
                                       class="mb-2 block text-sm
                                              font-medium text-[#331344]">
                                    Categoría *
                                </label>

                                <select
                                    id="product-category"
                                    wire:model="categoryId"
                                    required
                                    class="min-h-12 w-full rounded-xl
                                           border border-[#E3DCE7]
                                           bg-[#F8F7FA] px-4
                                           text-sm outline-none
                                           focus:border-[#563369]">

                                    <option value="">
                                        Seleccionar categoría
                                    </option>

                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('categoryId')
                                    <p class="mt-2 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div>
                            <label for="product-description"
                                   class="mb-2 block text-sm
                                          font-medium text-[#331344]">
                                Descripción
                            </label>

                            <textarea
                                id="product-description"
                                wire:model="description"
                                rows="4"
                                maxlength="5000"
                                placeholder="Describe los materiales, características y beneficios del uniforme..."
                                class="w-full rounded-xl border
                                       border-[#E3DCE7]
                                       bg-[#F8F7FA] px-4 py-3
                                       text-sm outline-none
                                       focus:border-[#563369]"></textarea>

                            @error('description')
                                <p class="mt-2 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </section>

                    <!-- PRECIOS -->
                    <section class="space-y-5">

                        <h3 class="border-b border-[#EAEBEE]
                                   pb-3 text-base font-semibold
                                   text-[#331344]">
                            02. Precios
                        </h3>

                        <div class="grid gap-5 sm:grid-cols-2">

                            <div>
                                <label for="product-price"
                                       class="mb-2 block text-sm
                                              font-medium text-[#331344]">
                                    Precio de venta ($) *
                                </label>

                                <input
                                    id="product-price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model="price"
                                    required
                                    placeholder="35.00"
                                    class="min-h-12 w-full rounded-xl
                                           border border-[#E3DCE7]
                                           bg-[#F8F7FA] px-4
                                           text-sm outline-none
                                           focus:border-[#563369]"
                                >

                                @error('price')
                                    <p class="mt-2 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="product-compare-price"
                                       class="mb-2 block text-sm
                                              font-medium text-[#331344]">
                                    Precio anterior ($)
                                </label>

                                <input
                                    id="product-compare-price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    wire:model="comparePrice"
                                    placeholder="45.00"
                                    class="min-h-12 w-full rounded-xl
                                           border border-[#E3DCE7]
                                           bg-[#F8F7FA] px-4
                                           text-sm outline-none
                                           focus:border-[#563369]"
                                >

                                @error('comparePrice')
                                    <p class="mt-2 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <p class="text-xs text-[#817689]">
                            El precio anterior es opcional.
                            Debe ser mayor al precio de venta.
                        </p>
                    </section>

                    <!-- VARIANTES -->
                    <section class="space-y-5">

                        <div class="flex flex-wrap
                                    items-center justify-between
                                    gap-3 border-b border-[#EAEBEE]
                                    pb-3">

                            <div>
                                <h3 class="text-base font-semibold
                                           text-[#331344]">
                                    03. Tallas, colores y stock
                                </h3>

                                <p class="mt-1 text-xs text-[#716779]">
                                    Cada combinación tiene un SKU único.
                                </p>
                            </div>

                            <button
                                type="button"
                                wire:click="addVariant"
                                class="min-h-10 rounded-xl
                                       border border-[#331344]
                                       px-4 text-xs font-semibold
                                       text-[#331344]
                                       transition hover:bg-[#F8F7FA]">
                                + Agregar variante
                            </button>
                        </div>

                        @error('variants')
                            <p class="text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <div class="space-y-4">

                            @foreach ($variants as $index => $variant)

                                <div wire:key="variant-{{ $variant['id'] ?? 'new-'.$index }}"
                                     class="rounded-2xl
                                            border border-[#E3DCE7]
                                            bg-[#FCFAFD] p-4 sm:p-5">

                                    <div class="mb-5 flex items-center
                                                justify-between gap-3">

                                        <h4 class="text-sm font-semibold
                                                   text-[#331344]">
                                            Variante {{ $index + 1 }}
                                        </h4>

                                        <button
                                            type="button"
                                            wire:click="removeVariant({{ $index }})"
                                            class="rounded-lg border
                                                   border-red-100 px-3 py-2
                                                   text-xs font-medium
                                                   text-red-600
                                                   transition hover:bg-red-50">
                                            Quitar
                                        </button>
                                    </div>

                                    <div class="grid gap-4
                                                sm:grid-cols-2">

                                        <!-- SKU -->
                                        <div>
                                            <label class="mb-2 block
                                                          text-xs font-semibold
                                                          text-[#563369]">
                                                Código SKU *
                                            </label>

                                            <input
                                                type="text"
                                                wire:model="variants.{{ $index }}.sku"
                                                maxlength="70"
                                                placeholder="VK-VIC-S-VIN"
                                                class="min-h-11 w-full
                                                       rounded-xl border
                                                       border-[#E3DCE7]
                                                       bg-white px-3
                                                       text-sm outline-none
                                                       focus:border-[#563369]"
                                            >

                                            @error("variants.$index.sku")
                                                <p class="mt-2 text-xs
                                                          text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- TALLA CON CHECKLIST Y AGREGAR OTRA -->
                                        <div>
                                            @php
                                                $selectedSizesList = array_filter(array_map('trim', explode(',', $variant['size'] ?? '')));
                                            @endphp
                                            <div class="mb-2 flex items-center justify-between">
                                                <label class="block text-xs font-semibold text-[#563369]">
                                                    Tallas disponibles * (puedes marcar varias)
                                                </label>
                                                <span class="text-[11px] font-bold text-[#331344]">
                                                    {{ !empty($selectedSizesList) ? 'Seleccionadas: '.implode(', ', $selectedSizesList) : 'Elige una o más tallas' }}
                                                </span>
                                            </div>

                                            {{-- Checklist de tallas en botones/píldoras seleccionables múltiples --}}
                                            <div class="flex flex-wrap gap-1.5 mb-2">
                                                @foreach ($commonSizes as $s)
                                                    @php
                                                        $isMarked = in_array($s, $selectedSizesList, true);
                                                    @endphp
                                                    <button
                                                        type="button"
                                                        wire:click="selectVariantSize({{ $index }}, '{{ $s }}')"
                                                        class="min-h-8 min-w-8 px-2.5 py-1 text-xs font-bold rounded-lg border transition-all flex items-center gap-1.5
                                                            {{ $isMarked
                                                                ? 'border-[#331344] bg-[#331344] text-white shadow-sm ring-1 ring-[#331344]'
                                                                : 'border-[#E3DCE7] bg-white text-gray-700 hover:border-[#563369] hover:text-[#563369]' }}">
                                                        <span>{{ $isMarked ? '✓' : '+' }}</span>
                                                        <span>{{ $s }}</span>
                                                    </button>
                                                @endforeach
                                            </div>

                                            {{-- Opción para agregar una talla que no esté en la lista --}}
                                            <div class="flex items-center gap-2">
                                                <input
                                                    type="text"
                                                    wire:model.defer="newSizeInputs.{{ $index }}"
                                                    wire:keydown.enter.prevent="addCustomSize({{ $index }})"
                                                    placeholder="Agregar otra talla (ej: 4XL, 32...)"
                                                    class="min-h-9 flex-1 rounded-lg border border-[#E3DCE7] bg-white px-2.5 text-xs outline-none focus:border-[#563369]"
                                                >
                                                <button
                                                    type="button"
                                                    wire:click="addCustomSize({{ $index }})"
                                                    class="min-h-9 px-3 rounded-lg border border-[#563369] bg-[#F8F7FA] text-xs font-semibold text-[#563369] hover:bg-[#563369] hover:text-white transition">
                                                    + Añadir
                                                </button>
                                            </div>

                                            <input type="hidden" wire:model="variants.{{ $index }}.size">

                                            @error("variants.$index.size")
                                                <p class="mt-2 text-xs text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- COLOR -->
                                        <div>
                                            <label class="mb-2 block
                                                          text-xs font-semibold
                                                          text-[#563369]">
                                                Color (opcional)
                                            </label>

                                            <input
                                                type="text"
                                                wire:model="variants.{{ $index }}.color_name"
                                                maxlength="60"
                                                placeholder="Vacío para diseños personalizados"
                                                class="min-h-11 w-full
                                                       rounded-xl border
                                                       border-[#E3DCE7]
                                                       bg-white px-3
                                                       text-sm outline-none
                                                       focus:border-[#563369]"
                                            >

                                            @error("variants.$index.color_name")
                                                <p class="mt-2 text-xs
                                                          text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- COLOR HEXADECIMAL -->
                                        <div>
                                            <label class="mb-2 block
                                                          text-xs font-semibold
                                                          text-[#563369]">
                                                Código de color
                                            </label>

                                            <input
                                                type="text"
                                                wire:model="variants.{{ $index }}.color_hex"
                                                maxlength="7"
                                                placeholder="#800020"
                                                class="min-h-11 w-full
                                                       rounded-xl border
                                                       border-[#E3DCE7]
                                                       bg-white px-3
                                                       text-sm outline-none
                                                       focus:border-[#563369]"
                                            >

                                            @error("variants.$index.color_hex")
                                                <p class="mt-2 text-xs
                                                          text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- STOCK -->
                                        <div>
                                            <label class="mb-2 block
                                                          text-xs font-semibold
                                                          text-[#563369]">
                                                Unidades disponibles *
                                            </label>

                                            <input
                                                type="number"
                                                min="0"
                                                step="1"
                                                wire:model="variants.{{ $index }}.stock"
                                                class="min-h-11 w-full
                                                       rounded-xl border
                                                       border-[#E3DCE7]
                                                       bg-white px-3
                                                       text-sm outline-none
                                                       focus:border-[#563369]"
                                            >

                                            @error("variants.$index.stock")
                                                <p class="mt-2 text-xs
                                                          text-red-600">
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        <!-- ESTADO -->
                                        <div class="flex items-center">
                                            <label class="flex items-center
                                                          gap-3 text-sm
                                                          text-[#331344]">

                                                <input
                                                    type="checkbox"
                                                    wire:model="variants.{{ $index }}.is_active"
                                                    class="h-5 w-5 rounded
                                                           border-[#D9CDDF]
                                                           accent-[#331344]"
                                                >

                                                Variante disponible
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        </div>
                    </section>

                    <!-- CONFIGURACIÓN -->
                    <section class="space-y-5">

                        <h3 class="border-b border-[#EAEBEE]
                                   pb-3 text-base font-semibold
                                   text-[#331344]">
                            04. Configuración comercial
                        </h3>

                        <div class="grid gap-4 sm:grid-cols-2">

                            <label class="flex items-center gap-3
                                          rounded-xl bg-[#F8F7FA]
                                          p-4">

                                <input
                                    type="checkbox"
                                    wire:model="isActive"
                                    class="h-5 w-5 rounded
                                           accent-[#331344]"
                                >

                                <span class="text-sm font-medium
                                             text-[#331344]">
                                    Producto activo
                                </span>
                            </label>

                            <label class="flex items-center gap-3
                                          rounded-xl bg-[#F8F7FA]
                                          p-4">

                                <input
                                    type="checkbox"
                                    wire:model="isFeatured"
                                    class="h-5 w-5 rounded
                                           accent-[#331344]"
                                >

                                <span class="text-sm font-medium
                                             text-[#331344]">
                                    Producto destacado
                                </span>
                            </label>
                        </div>

                        <div class="max-w-xs">
                            <label for="product-order"
                                   class="mb-2 block text-sm
                                          font-medium text-[#331344]">
                                Orden de visualización
                            </label>

                            <input
                                id="product-order"
                                type="number"
                                min="0"
                                max="9999"
                                wire:model="sortOrder"
                                class="min-h-12 w-full rounded-xl
                                       border border-[#E3DCE7]
                                       bg-[#F8F7FA] px-4
                                       text-sm outline-none
                                       focus:border-[#563369]"
                            >

                            @error('sortOrder')
                                <p class="mt-2 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </section>

                    <!-- BOTONES DEL FORMULARIO -->
                    <div class="flex flex-col-reverse gap-3
                                border-t border-[#EAEBEE] pt-6
                                sm:flex-row sm:justify-end">

                        <button
                            type="button"
                            wire:click="cancelForm"
                            class="min-h-12 rounded-xl
                                   border border-[#E3DCE7]
                                   px-7 text-sm font-semibold
                                   text-[#331344]">

                            Cancelar
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="min-h-12 rounded-xl
                                   bg-[#331344] px-7
                                   text-sm font-semibold text-white
                                   transition hover:bg-[#563369]
                                   disabled:opacity-60">

                            <span wire:loading.remove
                                  wire:target="save">

                                {{ $editingId
                                    ? 'Guardar cambios'
                                    : 'Crear producto' }}
                            </span>

                            <span wire:loading
                                  wire:target="save">
                                Guardando...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
