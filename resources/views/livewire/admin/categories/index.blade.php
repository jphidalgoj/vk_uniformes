
<div class="space-y-7">

    <!-- =====================================
         ENCABEZADO
    ====================================== -->
    <section class="flex flex-col gap-5
                    sm:flex-row sm:items-center
                    sm:justify-between">

        <div>
            <span class="text-xs font-semibold uppercase
                         tracking-widest text-[#563369]">
                Administración / Catálogo
            </span>

            <h1 class="mt-2 text-3xl font-semibold
                       tracking-tight text-[#230C31]">
                Categorías
            </h1>

            <p class="mt-2 text-sm text-[#716779]">
                Organiza las colecciones de VK Uniformes.
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
            Nueva categoría
        </button>
    </section>

    <!-- =====================================
         NOTIFICACIONES
    ====================================== -->
    @if ($notice)
        <div class="flex items-center justify-between
                    gap-3 rounded-xl border
                    border-[#DED0E6] bg-[#F5EEF8]
                    px-4 py-3 text-sm text-[#331344]"
             role="status">

            <span>{{ $notice }}</span>

            <button type="button"
                    wire:click="$set('notice', '')"
                    aria-label="Cerrar notificación"
                    class="font-bold">
                &times;
            </button>
        </div>
    @endif

    <!-- =====================================
         ESTADÍSTICAS
    ====================================== -->
    <section class="grid grid-cols-2 gap-3 sm:gap-5">

        <div class="rounded-2xl border border-[#EAEBEE]
                    bg-white p-5 shadow-sm">

            <p class="text-sm text-[#716779]">
                Total de categorías
            </p>

            <p class="mt-3 text-3xl font-bold text-[#331344]">
                {{ $totalCategories }}
            </p>
        </div>

        <div class="rounded-2xl border border-[#EAEBEE]
                    bg-white p-5 shadow-sm">

            <p class="text-sm text-[#716779]">
                Categorías activas
            </p>

            <p class="mt-3 text-3xl font-bold text-[#331344]">
                {{ $activeCategories }}
            </p>
        </div>
    </section>

    <!-- =====================================
         BUSCADOR Y FILTROS
    ====================================== -->
    <section class="rounded-2xl border border-[#EAEBEE]
                    bg-white p-4 shadow-sm sm:p-5">

        <div class="grid gap-3 sm:grid-cols-[1fr_190px]">

            <div>
                <label for="category-search"
                       class="sr-only">
                    Buscar categorías
                </label>

                <input
                    id="category-search"
                    type="search"
                    wire:model.live.debounce.350ms="search"
                    placeholder="Buscar categoría..."
                    class="min-h-12 w-full rounded-xl
                           border border-[#E3DCE7]
                           bg-[#F8F7FA] px-4
                           text-sm text-[#230C31]
                           outline-none
                           focus:border-[#563369]"
                >
            </div>

            <div>
                <label for="category-status"
                       class="sr-only">
                    Filtrar por estado
                </label>

                <select
                    id="category-status"
                    wire:model.live="status"
                    class="min-h-12 w-full rounded-xl
                           border border-[#E3DCE7]
                           bg-white px-4 text-sm
                           text-[#331344]
                           outline-none
                           focus:border-[#563369]">

                    <option value="all">Todas</option>
                    <option value="active">Activas</option>
                    <option value="inactive">Inactivas</option>
                </select>
            </div>
        </div>
    </section>

    <!-- =====================================
         LISTADO DE CATEGORÍAS
    ====================================== -->
    <section class="space-y-3">

        @forelse ($categories as $category)

            <article
                wire:key="category-{{ $category->id }}"
                class="flex flex-col gap-4 rounded-2xl
                       border border-[#EAEBEE]
                       bg-white p-4 shadow-sm
                       transition hover:shadow-md
                       sm:flex-row sm:items-center
                       sm:justify-between">

                <!-- IMAGEN E INFORMACIÓN -->
                <div class="flex items-center gap-4">

                    <div class="h-20 w-20 shrink-0
                                overflow-hidden rounded-xl
                                bg-[#F2EDF5]">

                        @if ($category->image_path)

                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->image_path) }}"
                                alt="{{ $category->name }}"
                                loading="lazy"
                                class="h-full w-full object-cover"
                            >

                        @else

                            <div class="flex h-full w-full
                                        items-center justify-center
                                        text-xl font-bold
                                        text-[#A99AB4]">
                                VK
                            </div>

                        @endif
                    </div>

                    <div class="min-w-0">

                        <h3 class="font-semibold text-[#230C31]">
                            {{ $category->name }}
                        </h3>

                        <p class="mt-1 break-all text-xs
                                  text-[#8B8191]">
                            /{{ $category->slug }}
                        </p>

                        <p class="mt-2 text-xs text-[#716779]">
                            Orden: {{ $category->sort_order }}
                        </p>
                    </div>
                </div>

                <!-- ESTADO Y ACCIONES -->
                <div class="flex flex-wrap items-center
                            justify-between gap-3
                            border-t border-[#F0ECF2]
                            pt-4 sm:justify-end
                            sm:border-0 sm:pt-0">

                    <button
                        type="button"
                        wire:click="toggleActive({{ $category->id }})"
                        aria-label="Cambiar visibilidad de {{ $category->name }}"
                        class="rounded-full px-3 py-2
                               text-xs font-semibold
                               transition
                               {{ $category->is_active
                                    ? 'bg-[#F1E9F5] text-[#331344]'
                                    : 'bg-slate-100 text-slate-500' }}">

                        {{ $category->is_active
                            ? '● Activa'
                            : '○ Inactiva' }}
                    </button>

                    <div class="flex items-center gap-2">

                        <button
                            type="button"
                            wire:click="edit({{ $category->id }})"
                            class="min-h-10 rounded-lg
                                   border border-[#E3DCE7]
                                   px-4 text-sm font-medium
                                   text-[#331344]
                                   transition hover:bg-[#F8F7FA]">
                            Editar
                        </button>

                        <button
                            type="button"
                            wire:click="delete({{ $category->id }})"
                            wire:confirm="¿Eliminar esta categoría definitivamente?"
                            class="min-h-10 rounded-lg
                                   border border-red-100
                                   px-4 text-sm font-medium
                                   text-red-600
                                   transition hover:bg-red-50">
                            Eliminar
                        </button>
                    </div>
                </div>
            </article>

        @empty

            <div class="rounded-2xl border
                        border-dashed border-[#D9CDDF]
                        bg-white px-6 py-16 text-center">

                <p class="text-lg font-semibold text-[#331344]">
                    No se encontraron categorías
                </p>

                <p class="mt-2 text-sm text-[#716779]">
                    Modifica los filtros o crea una categoría.
                </p>
            </div>

        @endforelse
    </section>

    <!-- PAGINACIÓN -->
    <div>
        {{ $categories->links() }}
    </div>

    <!-- =====================================
         FORMULARIO CREAR / EDITAR
    ====================================== -->
    @if ($showForm)

        <div class="fixed inset-0 z-[100]
                    overflow-y-auto bg-[#16081F]/75
                    px-3 py-5 backdrop-blur-sm
                    sm:px-6 sm:py-10">

            <div role="dialog"
                 aria-modal="true"
                 aria-labelledby="category-form-title"
                 class="mx-auto w-full max-w-xl
                        overflow-hidden rounded-2xl
                        bg-white shadow-2xl">

                <!-- ENCABEZADO DEL FORMULARIO -->
                <div class="flex items-center justify-between
                            border-b border-[#EAEBEE]
                            px-5 py-5 sm:px-7">

                    <div>
                        <h2 id="category-form-title"
                            class="text-xl font-semibold
                                   text-[#230C31]">

                            {{ $editingId
                                ? 'Editar categoría'
                                : 'Nueva categoría' }}
                        </h2>

                        <p class="mt-1 text-xs text-[#716779]">
                            Configura la información de la colección.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="cancelForm"
                        aria-label="Cerrar formulario"
                        class="flex h-10 w-10 items-center
                               justify-center rounded-xl
                               bg-[#F8F7FA] text-xl
                               text-[#331344]">
                        &times;
                    </button>
                </div>

                <!-- FORMULARIO LIVEWIRE -->
                <form wire:submit="save"
                      class="space-y-5 px-5 py-6 sm:px-7">

                    <!-- NOMBRE -->
                    <div>
                        <label for="category-name"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Nombre de la categoría *
                        </label>

                        <input
                            id="category-name"
                            type="text"
                            wire:model="name"
                            maxlength="120"
                            required
                            placeholder="Ej. Scrub Básico"
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

                    <!-- DESCRIPCIÓN -->
                    <div>
                        <label for="category-description"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Descripción
                        </label>

                        <textarea
                            id="category-description"
                            wire:model="description"
                            rows="3"
                            maxlength="1000"
                            placeholder="Describe esta colección..."
                            class="w-full rounded-xl
                                   border border-[#E3DCE7]
                                   bg-[#F8F7FA] px-4 py-3
                                   text-sm outline-none
                                   focus:border-[#563369]"></textarea>

                        @error('description')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- IMAGEN -->
                    <div>
                        <label for="category-image"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Fotografía de la categoría
                        </label>

                        <input
                            id="category-image"
                            type="file"
                            wire:model="image"
                            accept="image/jpeg,image/png,image/webp"
                            class="block w-full rounded-xl
                                   border border-[#E3DCE7]
                                   bg-[#F8F7FA] p-3
                                   text-xs text-[#716779]
                                   file:mr-3 file:rounded-lg
                                   file:border-0
                                   file:bg-[#331344]
                                   file:px-3 file:py-2
                                   file:font-semibold
                                   file:text-white"
                        >

                        <p class="mt-2 text-xs text-[#8B8191]">
                            JPG, PNG o WebP. Máximo 3 MB.
                        </p>

                        <p wire:loading wire:target="image"
                           class="mt-2 text-xs text-[#563369]">
                            Subiendo imagen...
                        </p>

                        @error('image')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <!-- VISTA PREVIA NUEVA IMAGEN -->
                        @if (
                            $image &&
                            in_array(
                                $image->getMimeType(),
                                ['image/jpeg', 'image/png', 'image/webp'],
                                true
                            )
                        )
                            @php
                                $previewUrl = null;
                                try {
                                    $previewUrl = $image->temporaryUrl();
                                } catch (\Throwable $e) {
                                    $previewUrl = null;
                                }
                            @endphp
                            @if ($previewUrl)
                                <img
                                    src="{{ $previewUrl }}"
                                    alt="Vista previa"
                                    class="mt-4 h-36 w-36 rounded-xl object-cover"
                                    onerror="this.style.display='none';"
                                >
                            @else
                                <div class="mt-4 flex h-36 w-36 flex-col items-center justify-center rounded-xl bg-purple-50 p-2 text-center text-xs text-purple-900">
                                    <span class="text-2xl">📷</span>
                                    <span class="mt-1 truncate max-w-full font-semibold">{{ $image->getClientOriginalName() }}</span>
                                </div>
                            @endif
                        @elseif ($currentImage && ! $removeImage)

                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($currentImage) }}"
                                alt="Imagen actual"
                                class="mt-4 h-36 w-36 rounded-xl
                                       object-cover"
                            >
                        @endif

                        @if ($currentImage)
                            <label class="mt-3 flex items-center gap-2
                                          text-sm text-[#716779]">

                                <input
                                    type="checkbox"
                                    wire:model="removeImage"
                                    class="rounded border-[#D9CDDF]
                                           text-[#331344]">

                                Quitar imagen actual
                            </label>
                        @endif
                    </div>

                    <!-- ORDEN -->
                    <div>
                        <label for="category-order"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Orden de visualización
                        </label>

                        <input
                            id="category-order"
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

                    <!-- VISIBILIDAD -->
                    <label class="flex items-center gap-3
                                  rounded-xl bg-[#F8F7FA] p-4">

                        <input
                            type="checkbox"
                            wire:model="isActive"
                            class="h-5 w-5 rounded
                                   border-[#D9CDDF]
                                   text-[#331344]">

                        <span>
                            <span class="block text-sm
                                         font-semibold text-[#331344]">
                                Categoría activa
                            </span>

                            <span class="mt-1 block text-xs
                                         text-[#716779]">
                                Mostrar esta categoría en la tienda.
                            </span>
                        </span>
                    </label>

                    <!-- ACCIONES -->
                    <div class="flex flex-col-reverse
                                gap-3 border-t border-[#EAEBEE]
                                pt-5 sm:flex-row">

                        <button
                            type="button"
                            wire:click="cancelForm"
                            class="min-h-12 flex-1 rounded-xl
                                   border border-[#E3DCE7]
                                   text-sm font-semibold
                                   text-[#331344]">
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="save,image"
                            class="min-h-12 flex-1 rounded-xl
                                   bg-[#331344] px-5
                                   text-sm font-semibold text-white
                                   transition hover:bg-[#563369]
                                   disabled:opacity-60">

                            <span wire:loading.remove
                                  wire:target="save,image">
                                {{ $editingId
                                    ? 'Guardar cambios'
                                    : 'Crear categoría' }}
                            </span>

                            <span wire:loading
                                  wire:target="save,image">
                                Procesando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
