<div class="space-y-8">
    <!-- ENCABEZADO -->
    <section>
        <a href="{{ route('admin.products.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-[#563369] hover:text-[#331344]">
            ← Volver al catálogo de productos
        </a>

        <span class="mt-4 block text-xs font-semibold uppercase tracking-widest text-[#716779]">
            Administración / Catálogo / Fotografías por color
        </span>

        <h1 class="mt-2 text-3xl font-bold text-[#230C31]">
            Fotografías: {{ $product->name }}
        </h1>

        <p class="mt-2 text-sm text-[#716779]">
            Puedes subir <strong>hasta 8 fotografías generales sin color</strong> para diseños personalizados y hasta 4 fotografías por cada color. Cuando el usuario navegue en la tienda y seleccione un color, verá inmediatamente las fotografías de esa prenda en dicho tono.
        </p>
    </section>

    <!-- NOTIFICACIÓN -->
    @if ($notice)
        <div role="status"
             class="flex items-center justify-between gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-900 shadow-sm">
            <span>{{ $notice }}</span>
            <button type="button" wire:click="$set('notice', '')" aria-label="Cerrar notificación" class="text-xl font-bold">
                &times;
            </button>
        </div>
    @endif

    <!-- PANEL RESUMEN: COLORES Y SUS FOTOGRAFÍAS -->
    <section class="rounded-2xl border border-purple-100 bg-gradient-to-br from-purple-50/60 to-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-[#230C31] flex items-center gap-2">
            <span>🎨</span> Colores creados en este producto
        </h2>
        <p class="mt-1 text-xs text-gray-600">
            Cada color admite hasta 4 fotos. Haz clic en "Subir fotos para este color" para seleccionarlo rápidamente.
        </p>

        <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($colorsData as $cData)
                <div class="rounded-xl border {{ $photoColor === $cData['name'] ? 'border-purple-600 bg-purple-50 ring-2 ring-purple-600/20' : 'border-gray-200 bg-white' }} p-4 shadow-sm transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="h-5 w-5 rounded-full border border-black/10 shadow-inner shrink-0" style="background-color: {{ $cData['hex'] }}"></span>
                            <span class="font-bold text-sm text-[#230C31]">{{ $cData['name'] }}</span>
                        </div>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $cData['count'] >= 4 ? 'bg-amber-100 text-amber-800' : ($cData['count'] > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600') }}">
                            {{ $cData['count'] }} / 4 fotos
                        </span>
                    </div>

                    <!-- MINIATURAS DEL COLOR -->
                    <div class="mt-3 flex items-center gap-2 min-h-[52px]">
                        @forelse ($cData['images'] as $img)
                            <div class="relative h-12 w-10 shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($img->path) }}" alt="{{ $cData['name'] }}" class="h-full w-full object-cover">
                                @if ($img->is_primary)
                                    <span class="absolute bottom-0 inset-x-0 bg-[#331344] text-[8px] text-center text-white font-bold leading-none py-0.5">★</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-[11px] text-gray-400 italic">Sin fotos para este color</p>
                        @endforelse
                    </div>

                    <div class="mt-3 pt-3 border-t flex items-center justify-between gap-2">
                        @if ($cData['remaining'] > 0)
                            <button type="button" wire:click="selectColor({{ Illuminate\Support\Js::from($cData['name']) }})" class="w-full rounded-lg bg-[#331344] py-1.5 px-3 text-xs font-semibold text-white transition hover:bg-[#563369]">
                                Subir fotos para {{ $cData['name'] }} ({{ $cData['remaining'] }} libres)
                            </button>
                        @else
                            <span class="w-full text-center text-xs font-medium text-amber-700 bg-amber-50 py-1.5 rounded-lg border border-amber-200">
                                ✓ Límite de 4 fotos completado
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                    Este producto no necesita colores. Sube hasta 8 fotografías generales de su diseño personalizado.
                </div>
            @endforelse
        </div>
    </section>

    <div class="rounded-xl border border-[#E3DCE7] p-4">
        <p class="text-sm font-bold text-[#230C31]">Fotografías generales / Estilo personalizado: {{ $generalImages->count() }} / {{ $maxGeneral }} fotos</p>
        <p class="text-xs text-gray-500">Para productos de diseño único o prendas sin colores (hasta 8 fotos permitidas)</p>
        @if ($generalImages->count() < $maxGeneral)
            <button type="button" wire:click="selectColor('')" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-[#331344] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#563369]">
                Subir fotos para Estilo personalizado / General ({{ max(0, $maxGeneral - $generalImages->count()) }} disponibles)
            </button>
        @else
            <span class="mt-3 inline-block rounded-lg bg-amber-50 border border-amber-200 px-3 py-1.5 text-xs font-semibold text-amber-800">
                ✓ Límite de {{ $maxGeneral }} fotos completado
            </span>
        @endif
        @error('color') <p role="alert" class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <!-- FORMULARIO DE SUBIDA DE FOTOGRAFÍAS -->
    <section class="rounded-2xl border border-[#EAEBEE] bg-white p-6 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#331344]">
                    Subir fotografías para: <span class="text-purple-700">{{ $photoColor ?: 'Fotografías generales' }}</span>
                </h2>
                <p class="mt-1 text-xs text-[#716779]">
                    Este grupo admite hasta {{ $selectedLimit }} fotografías en total. Formatos permitidos: JPG, PNG o WebP (máx. 3 MB c/u).
                </p>
            </div>

            <span class="rounded-full bg-[#F1E9F5] px-4 py-1.5 text-xs font-bold text-[#331344]">
                Total catálogo: {{ $images->count() }} / 48 fotos
            </span>
        </div>

        <form wire:submit="savePhotos" class="mt-6 space-y-6">
            <div>
                <label for="photo-color" class="block text-sm font-bold text-gray-700">
                    Tipo de fotografías (el color es opcional):
                </label>
                <select id="photo-color" wire:model.live="photoColor" class="mt-2 w-full rounded-xl border border-gray-300 p-3.5 text-sm font-medium focus:border-purple-600 focus:outline-none">
                    <option value="">Fotos generales / diseño personalizado (hasta 8)</option>
                    @foreach ($colors as $color)
                        @php
                            $currCount = $images->where('color_name', $color)->count();
                        @endphp
                        <option value="{{ $color }}">
                            Color: {{ $color }} ({{ $currCount }}/4 fotos registradas)
                        </option>
                    @endforeach
                </select>
                @error('photoColor') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="rounded-2xl border-2 border-dashed border-[#D9CDDF] bg-[#FCFAFD] p-6 text-center hover:bg-purple-50/30 transition">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-[#F1E9F5] text-[#331344]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="m21 15-5-5L5 21"/>
                    </svg>
                </div>

                <h3 class="mt-3 text-sm font-bold text-[#331344]">
                    Selecciona fotografías para "{{ $photoColor ?: 'General' }}"
                </h3>

                <p class="mt-1 text-xs text-gray-500">
                    Máximo {{ $selectedLimit }} fotos en este grupo. JPG, PNG o WebP de hasta 3 MB.
                </p>

                <input
                    type="file"
                    wire:model="photos"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    aria-label="Seleccionar fotografías del producto"
                    class="mt-4 block w-full rounded-xl border border-[#E3DCE7] bg-white p-3 text-xs text-[#563369] file:mr-3 file:rounded-lg file:border-0 file:bg-[#331344] file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white"
                >

                <p wire:loading wire:target="photos" class="mt-3 text-xs font-semibold text-purple-600">
                    Cargando vista previa de fotografías…
                </p>

                @error('photos')
                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                @enderror
                @error('photos.*')
                    <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- VISTAS PREVIAS DE SUBIDA -->
            @if (count($photos) > 0)
                <div>
                    <h3 class="mb-2 text-xs font-bold uppercase tracking-wider text-[#331344]">
                        Fotografías seleccionadas para subir ({{ count($photos) }})
                    </h3>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ($photos as $index => $photo)
                            @if (in_array($photo->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true))
                                <div wire:key="preview-{{ $index }}" class="overflow-hidden rounded-xl border border-purple-200 bg-gray-50">
                                    @php
                                        $previewUrl = null;
                                        try {
                                            $previewUrl = $photo->temporaryUrl();
                                        } catch (\Throwable $e) {
                                            $previewUrl = null;
                                        }
                                    @endphp
                                    @if ($previewUrl)
                                        <img src="{{ $previewUrl }}" alt="Previa {{ $index + 1 }}" class="aspect-[4/5] w-full object-cover" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                                    @endif
                                    <div class="aspect-[4/5] w-full flex flex-col items-center justify-center p-2 text-center text-xs text-gray-500" @if($previewUrl) style="display:none;" @endif>
                                        <span class="text-xl">📷</span>
                                        <span class="mt-1 font-semibold truncate max-w-full text-[11px]">{{ $photo->getClientOriginalName() }}</span>
                                        <span class="text-[9px] text-green-700 bg-green-50 px-2 py-0.5 rounded-full mt-1">Listo para guardar</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="photos,savePhotos"
                class="min-h-12 w-full rounded-xl bg-[#331344] px-6 text-sm font-bold text-white shadow-sm transition hover:bg-[#563369] disabled:opacity-50">
                <span wire:loading.remove wire:target="photos,savePhotos">
                    Guardar fotos para {{ $photoColor ?: 'General' }}
                </span>
                <span wire:loading wire:target="photos,savePhotos">
                    Guardando fotografías…
                </span>
            </button>
        </form>
    </section>

    <!-- GALERÍA COMPLETA ORGANIZADA -->
    <section class="rounded-2xl border border-[#EAEBEE] bg-white p-6 shadow-sm sm:p-8">
        <div class="flex items-center justify-between border-b pb-4">
            <div>
                <h2 class="text-lg font-bold text-[#331344]">
                    Fotografías registradas en el catálogo
                </h2>
                <p class="mt-1 text-xs text-[#716779]">
                    Organiza el orden de las fotos, reasigna su color o establece la foto de portada.
                </p>
            </div>
            <span class="text-xs text-gray-500 font-medium">
                {{ $images->count() }} fotografías
            </span>
        </div>

        @if ($images->isEmpty())
            <div class="mt-6 rounded-xl border border-dashed border-[#D9CDDF] bg-[#FCFAFD] p-12 text-center">
                <p class="text-sm font-medium text-[#716779]">
                    Este producto todavía no tiene fotografías registradas.
                </p>
            </div>
        @else
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($images as $image)
                    <article
                        wire:key="stored-image-{{ $image->id }}"
                        class="overflow-hidden rounded-2xl border {{ $image->is_primary ? 'border-purple-400 ring-2 ring-purple-400/20' : 'border-gray-200' }} bg-white shadow-sm">

                        <!-- FOTOGRAFÍA -->
                        <div class="relative aspect-[4/5] bg-gray-50 overflow-hidden">
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}"
                                alt="{{ $image->alt_text ?? $product->name }}"
                                loading="lazy"
                                class="h-full w-full object-cover"
                            >

                            <div class="absolute left-3 top-3 flex flex-col gap-1.5">
                                @if ($image->is_primary)
                                    <span class="rounded-full bg-[#331344] px-3 py-1 text-[11px] font-bold text-white shadow">
                                        ★ Portada principal
                                    </span>
                                @endif

                                @if ($image->color_name)
                                    <span class="rounded-full bg-white/95 px-3 py-1 text-[11px] font-bold text-[#331344] shadow backdrop-blur-sm">
                                        Color: {{ $image->color_name }}
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-900/80 px-3 py-1 text-[11px] font-medium text-white shadow backdrop-blur-sm">
                                        Foto general
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- REASIGNAR COLOR -->
                        <div class="p-4 border-b bg-gray-50/50">
                            <label for="image-color-{{ $image->id }}" class="block text-xs font-bold text-gray-700">
                                Asignar color de esta foto:
                            </label>
                            <select id="image-color-{{ $image->id }}"
                                    wire:change="setPhotoColor({{ $image->id }}, $event.target.value)"
                                    class="mt-1.5 w-full rounded-lg border border-gray-300 bg-white p-2.5 text-xs font-semibold text-[#331344] focus:border-purple-600 focus:outline-none">
                                <option value="" @selected(!$image->color_name)>Foto general (sin color)</option>
                                @foreach ($colors as $color)
                                    <option value="{{ $color }}" @selected($image->color_name === $color)>
                                        Color: {{ $color }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ACCIONES -->
                        <div class="space-y-2 p-4">
                            <button
                                type="button"
                                wire:click="setPrimary({{ $image->id }})"
                                @disabled($image->is_primary)
                                class="w-full rounded-xl border border-[#D9CDDF] py-2 text-xs font-bold text-[#331344] transition hover:bg-purple-50 disabled:opacity-50">
                                {{ $image->is_primary ? '✓ Es la portada principal' : 'Establecer como portada' }}
                            </button>

                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    wire:click="movePhoto({{ $image->id }}, 'up')"
                                    @disabled($loop->first)
                                    class="rounded-lg bg-gray-100 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200 disabled:opacity-30">
                                    ↑ Subir
                                </button>
                                <button
                                    type="button"
                                    wire:click="movePhoto({{ $image->id }}, 'down')"
                                    @disabled($loop->last)
                                    class="rounded-lg bg-gray-100 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200 disabled:opacity-30">
                                    ↓ Bajar
                                </button>
                            </div>

                            <button
                                type="button"
                                wire:click="deletePhoto({{ $image->id }})"
                                wire:confirm="¿Estás seguro de que deseas eliminar esta fotografía?"
                                class="w-full rounded-xl border border-red-100 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50">
                                Eliminar foto
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
