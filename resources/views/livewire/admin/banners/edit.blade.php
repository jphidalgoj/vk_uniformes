
<div class="space-y-7">

    <!-- ENCABEZADO -->
    <section class="flex flex-col gap-4
                    sm:flex-row sm:items-center
                    sm:justify-between">

        <div>
            <span class="text-xs font-semibold uppercase
                         tracking-widest text-[#563369]">
                Administración / Contenido web
            </span>

            <h1 class="mt-2 text-3xl font-semibold
                       text-[#230C31]">
                Banner principal
            </h1>

            <p class="mt-2 text-sm text-[#716779]">
                Personaliza la presentación de tu tienda.
            </p>
        </div>

        <a href="{{ route('home') }}"
           target="_blank"
           rel="noopener noreferrer"
           class="inline-flex min-h-11 items-center
                  justify-center rounded-xl border
                  border-[#D9CDDF] bg-white px-5
                  text-sm font-semibold text-[#331344]
                  transition hover:bg-[#F8F7FA]">
            Ver página principal ↗
        </a>
    </section>

    <!-- NOTIFICACIÓN -->
    @if ($notice)
        <div role="status"
             class="flex items-center justify-between
                    gap-3 rounded-xl border border-[#DED0E6]
                    bg-[#F5EEF8] p-4 text-sm text-[#331344]">

            <span>{{ $notice }}</span>

            <button type="button"
                    wire:click="$set('notice', '')"
                    aria-label="Cerrar notificación"
                    class="text-xl font-bold">
                &times;
            </button>
        </div>
    @endif

    <!-- FORMULARIO -->
    <form wire:submit="save"
          class="grid gap-6 lg:grid-cols-3">

        <!-- COLUMNA PRINCIPAL -->
        <div class="space-y-6 lg:col-span-2">

            <!-- INFORMACIÓN DEL BANNER -->
            <section class="rounded-2xl border
                            border-[#EAEBEE] bg-white
                            p-5 shadow-sm sm:p-7">

                <div class="mb-6">
                    <h2 class="text-lg font-semibold
                               text-[#230C31]">
                        Información principal
                    </h2>

                    <p class="mt-2 text-sm text-[#716779]">
                        Personaliza los textos que verán
                        tus clientes.
                    </p>
                </div>

                <div class="space-y-5">

                    <!-- TEXTO SUPERIOR -->
                    <div>
                        <label for="banner-eyebrow"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Texto superior
                        </label>

                        <input
                            id="banner-eyebrow"
                            type="text"
                            wire:model="eyebrow"
                            maxlength="100"
                            placeholder="VK UNIFORMES"
                            class="min-h-12 w-full rounded-xl
                                   border border-[#E3DCE7]
                                   bg-[#F8F7FA] px-4
                                   text-sm outline-none
                                   focus:border-[#331344]"
                        >

                        @error('eyebrow')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- TÍTULO PRINCIPAL -->
                    <div>
                        <label for="banner-title"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Título principal *
                        </label>

                        <input
                            id="banner-title"
                            type="text"
                            wire:model="title"
                            maxlength="200"
                            required
                            placeholder="Viste tu vocación con estilo."
                            class="min-h-12 w-full rounded-xl
                                   border border-[#E3DCE7]
                                   bg-[#F8F7FA] px-4
                                   text-sm outline-none
                                   focus:border-[#331344]"
                        >

                        @error('title')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div>
                        <label for="banner-description"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Descripción
                        </label>

                        <textarea
                            id="banner-description"
                            wire:model="description"
                            maxlength="2000"
                            rows="4"
                            placeholder="Describe tu colección..."
                            class="w-full rounded-xl border
                                   border-[#E3DCE7]
                                   bg-[#F8F7FA] px-4 py-3
                                   text-sm outline-none
                                   focus:border-[#331344]"></textarea>

                        @error('description')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- FOTOGRAFÍA -->
            <section class="rounded-2xl border
                            border-[#EAEBEE] bg-white
                            p-5 shadow-sm sm:p-7">

                <h2 class="text-lg font-semibold
                           text-[#230C31]">
                    Fotografía principal
                </h2>

                <p class="mt-2 text-sm text-[#716779]">
                    Utiliza una fotografía profesional
                    que represente a VK Uniformes.
                </p>

                <!-- VISTA PREVIA -->
                <div class="mt-6 overflow-hidden
                            rounded-2xl bg-[#F1EBF4]">

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
                                alt="Vista previa de nueva fotografía"
                                class="aspect-[5/4] w-full object-cover"
                                onerror="this.style.display='none';"
                            >
                        @else
                            <div class="flex aspect-[5/4] w-full flex-col items-center justify-center rounded-xl bg-purple-50 p-4 text-center text-xs text-purple-900">
                                <span class="text-3xl">📷</span>
                                <span class="mt-2 font-semibold">{{ $image->getClientOriginalName() }}</span>
                            </div>
                        @endif

                    @elseif ($banner->image_path && ! $removeImage)

                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($banner->image_path) }}"
                            alt="{{ $banner->image_alt ?: $banner->title }}"
                            class="aspect-[5/4] w-full object-cover"
                        >

                    @else

                        <div class="flex aspect-[5/4]
                                    flex-col items-center
                                    justify-center gap-3
                                    bg-[#331344]">

                            <span class="text-7xl font-semibold
                                         text-white/90">
                                VK
                            </span>

                            <span class="text-xs uppercase
                                         tracking-widest
                                         text-[#D9CFDF]">
                                Uniformes profesionales
                            </span>
                        </div>

                    @endif
                </div>

                <div class="mt-5 space-y-4">

                    <div>
                        <label for="banner-image"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Seleccionar fotografía
                        </label>

                        <input
                            id="banner-image"
                            type="file"
                            wire:model="image"
                            accept="image/jpeg,image/png,image/webp"
                            class="block w-full rounded-xl
                                   border border-[#E3DCE7]
                                   bg-[#F8F7FA] p-3 text-xs
                                   file:mr-3 file:rounded-lg
                                   file:border-0
                                   file:bg-[#331344]
                                   file:px-4 file:py-2
                                   file:text-white"
                        >

                        <p class="mt-2 text-xs text-[#817689]">
                            JPG, PNG o WebP. Máximo 3 MB.
                            Preferiblemente fotografía horizontal
                            con el sujeto centrado.
                        </p>

                        <p wire:loading wire:target="image"
                           class="mt-2 text-xs text-[#563369]">
                            Subiendo fotografía...
                        </p>

                        @error('image')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- TEXTO ALTERNATIVO -->
                    <div>
                        <label for="banner-alt"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Descripción de la fotografía
                        </label>

                        <input
                            id="banner-alt"
                            type="text"
                            wire:model="imageAlt"
                            maxlength="255"
                            placeholder="Profesional con uniforme VK"
                            class="min-h-12 w-full rounded-xl
                                   border border-[#E3DCE7]
                                   bg-[#F8F7FA] px-4
                                   text-sm outline-none
                                   focus:border-[#331344]"
                        >

                        @error('imageAlt')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    @if ($banner->image_path)
                        <label class="flex items-center gap-3
                                      rounded-xl bg-[#F8F7FA] p-4">

                            <input
                                type="checkbox"
                                wire:model.live="removeImage"
                                class="h-5 w-5 accent-[#331344]"
                            >

                            <span class="text-sm text-[#331344]">
                                Quitar fotografía actual
                            </span>
                        </label>
                    @endif
                </div>
            </section>
        </div>

        <!-- COLUMNA DE CONFIGURACIÓN -->
        <div class="space-y-6">

            <!-- BOTÓN -->
            <section class="rounded-2xl border
                            border-[#EAEBEE] bg-white
                            p-5 shadow-sm">

                <h2 class="text-lg font-semibold
                           text-[#230C31]">
                    Llamada a la acción
                </h2>

                <p class="mt-2 text-xs leading-6
                          text-[#716779]">
                    Configura el botón principal.
                </p>

                <div class="mt-5 space-y-5">

                    <div>
                        <label for="banner-button-text"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Texto del botón
                        </label>

                        <input
                            id="banner-button-text"
                            type="text"
                            wire:model="buttonText"
                            maxlength="100"
                            placeholder="Explorar colección"
                            class="min-h-12 w-full rounded-xl
                                   border border-[#E3DCE7]
                                   bg-[#F8F7FA] px-4
                                   text-sm outline-none
                                   focus:border-[#331344]"
                        >

                        @error('buttonText')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="banner-button-url"
                               class="mb-2 block text-sm
                                      font-semibold text-[#331344]">
                            Destino del botón
                        </label>

                        <select
                            id="banner-button-url"
                            wire:model="buttonUrl"
                            class="min-h-12 w-full rounded-xl
                                   border border-[#E3DCE7]
                                   bg-white px-4 text-sm
                                   outline-none
                                   focus:border-[#331344]">

                            <option value="/catalogo">
                                Catálogo completo
                            </option>

                            <option value="/">
                                Página de inicio
                            </option>

                            @foreach ($categories as $category)
                                <option
                                    value="/catalogo/{{ $category->slug }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('buttonUrl')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- VISIBILIDAD -->
            <section class="rounded-2xl border
                            border-[#EAEBEE] bg-white
                            p-5 shadow-sm">

                <h2 class="text-lg font-semibold
                           text-[#230C31]">
                    Publicación
                </h2>

                <label class="mt-5 flex items-center gap-3
                              rounded-xl bg-[#F8F7FA] p-4">

                    <input
                        type="checkbox"
                        wire:model="isActive"
                        class="h-5 w-5 accent-[#331344]"
                    >

                    <span>
                        <span class="block text-sm
                                     font-semibold text-[#331344]">
                            Banner activo
                        </span>

                        <span class="mt-1 block text-xs
                                     text-[#716779]">
                            Mostrar el banner en la tienda.
                        </span>
                    </span>
                </label>
            </section>

            <!-- GUARDAR -->
            <div class="rounded-2xl bg-[#331344] p-5">

                <p class="text-sm font-semibold text-white">
                    Publicar cambios
                </p>

                <p class="mt-2 text-xs leading-6
                          text-white/70">
                    Los cambios guardados aparecerán
                    en la página principal.
                </p>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save,image"
                    class="mt-5 min-h-12 w-full
                           rounded-xl bg-white px-5
                           text-sm font-semibold
                           text-[#331344] transition
                           hover:bg-[#EAEBEE]
                           disabled:opacity-50">

                    <span wire:loading.remove
                          wire:target="save,image">
                        Guardar cambios
                    </span>

                    <span wire:loading
                          wire:target="save,image">
                        Procesando...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
