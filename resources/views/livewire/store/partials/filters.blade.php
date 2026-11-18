
<div class="space-y-6">

    <!-- =====================================
         FILTRO POR TALLA
    ====================================== -->
    <div>
        <label for="{{ $prefix }}-size"
               class="mb-3 block text-sm font-semibold
                      text-[#331344]">
            Talla
        </label>

        <select
            id="{{ $prefix }}-size"
            wire:model.live="size"
            class="min-h-12 w-full rounded-xl
                   border border-[#E3DCE7]
                   bg-white px-4 text-sm
                   text-[#230C31] outline-none
                   focus:border-[#331344]">

            <option value="">Todas las tallas</option>

            @foreach ($availableSizes as $availableSize)
                <option value="{{ $availableSize }}">
                    {{ $availableSize }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- =====================================
         FILTRO POR COLOR
    ====================================== -->
    <div>
        <label for="{{ $prefix }}-color"
               class="mb-3 block text-sm font-semibold
                      text-[#331344]">
            Color
        </label>

        <select
            id="{{ $prefix }}-color"
            wire:model.live="color"
            class="min-h-12 w-full rounded-xl
                   border border-[#E3DCE7]
                   bg-white px-4 text-sm
                   text-[#230C31] outline-none
                   focus:border-[#331344]">

            <option value="">Todos los colores</option>

            @foreach ($availableColors as $availableColor)
                <option value="{{ $availableColor }}">
                    {{ $availableColor }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- =====================================
         FILTRO POR PRECIO
    ====================================== -->
    <div class="space-y-3">

        <h3 class="text-sm font-semibold
                   text-[#331344]">
            Rango de precios
        </h3>

        <div class="grid grid-cols-2 gap-3">

            <div>
                <label for="{{ $prefix }}-min"
                       class="mb-2 block text-xs
                              text-[#716779]">
                    Desde ($)
                </label>

                <input
                    id="{{ $prefix }}-min"
                    type="number"
                    step="0.01"
                    min="0"
                    max="99999999.99"
                    wire:model.live.debounce.500ms="minPrice"
                    placeholder="0.00"
                    class="min-h-12 w-full min-w-0
                           rounded-xl border
                           border-[#E3DCE7]
                           bg-white px-3 text-sm
                           text-[#230C31] outline-none
                           focus:border-[#331344]"
                >
            </div>

            <div>
                <label for="{{ $prefix }}-max"
                       class="mb-2 block text-xs
                              text-[#716779]">
                    Hasta ($)
                </label>

                <input
                    id="{{ $prefix }}-max"
                    type="number"
                    step="0.01"
                    min="0"
                    max="99999999.99"
                    wire:model.live.debounce.500ms="maxPrice"
                    placeholder="100.00"
                    class="min-h-12 w-full min-w-0
                           rounded-xl border
                           border-[#E3DCE7]
                           bg-white px-3 text-sm
                           text-[#230C31] outline-none
                           focus:border-[#331344]"
                >
            </div>
        </div>
    </div>

    <!-- =====================================
         FILTRO POR DISPONIBILIDAD
    ====================================== -->
    <label class="flex items-center gap-3
                  rounded-xl bg-[#F8F7FA]
                  p-4">

        <input
            type="checkbox"
            wire:model.live="onlyInStock"
            class="h-5 w-5 rounded
                   accent-[#331344]"
        >

        <span class="text-sm font-medium
                     text-[#331344]">
            Solo productos disponibles
        </span>
    </label>

    <!-- =====================================
         LIMPIAR FILTROS
    ====================================== -->
    <button
        type="button"
        wire:click="clearFilters"
        @disabled($activeFilters === 0)
        class="min-h-11 w-full rounded-xl
               border border-[#D9CDDF]
               bg-white px-4 text-sm
               font-semibold text-[#331344]
               transition hover:bg-[#F8F7FA]
               disabled:cursor-not-allowed
               disabled:opacity-40">

        Limpiar filtros

        @if ($activeFilters > 0)
            ({{ $activeFilters }})
        @endif
    </button>
</div>
