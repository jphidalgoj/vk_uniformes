<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-[#230C31]">Diseño de la portada</h1>
            <p class="mt-2 text-sm text-gray-600">Personaliza las secciones de la tienda: carrusel principal, presentaciones 50/50 texto e imagen, videos continuos, colecciones y catálogo.</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-semibold text-[#563369] underline hover:text-[#230C31]">
            Ver tienda en vivo ↗
        </a>
    </div>

    @if ($notice)
        <p role="status" class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-900 shadow-sm">
            {{ $notice }}
        </p>
    @endif

    @if ($errors->any())
        <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900 shadow-sm">
            <p class="font-semibold">No se pudo guardar. Revisa los siguientes campos:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-8 lg:grid-cols-[300px_1fr]">
        <!-- BARRA LATERAL CON SECCIONES -->
        <aside>
            <button wire:click="create" class="mb-4 flex w-full items-center justify-center gap-2 rounded-xl bg-[#230C31] p-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3d1852]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Añadir nueva sección
            </button>

            <div class="space-y-2">
                @foreach ($sections as $section)
                    <div wire:key="section-item-{{ $section->id }}" class="group relative flex items-center gap-2 rounded-xl border p-2 transition {{ $sectionId === $section->id ? 'border-purple-500 bg-purple-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                        <button type="button" wire:click="edit({{ $section->id }})" class="flex-1 text-left p-1">
                            <span class="block text-sm font-semibold text-[#230C31] truncate">
                                {{ $section->sort_order }} · {{ $section->title }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $section->is_active ? 'Visible' : 'Oculta' }} · 
                                @switch($section->content['type'] ?? 'hero')
                                    @case('split') Mitad texto / imagen @break
                                    @case('video') Video @break
                                    @case('hero') Carrusel hero @break
                                    @case('products') Productos @break
                                    @case('banner') Banner @break
                                    @case('categories') Categorías @break
                                    @case('lines') Líneas @break
                                    @default {{ $section->content['type'] ?? 'hero' }}
                                @endswitch
                            </span>
                        </button>

                        <!-- BOTÓN BORRAR SECCIÓN -->
                        <button type="button" 
                                wire:click="delete({{ $section->id }})" 
                                wire:confirm="¿Estás seguro de que deseas eliminar la sección '{{ $section->title }}' definitivamente?" 
                                title="Eliminar sección" 
                                aria-label="Eliminar sección {{ $section->title }}"
                                class="shrink-0 rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </aside>

        <!-- FORMULARIO DE EDICIÓN DE SECCIÓN -->
        <div class="min-w-0 space-y-8">
            <form wire:submit="save" class="space-y-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                <div class="flex items-center justify-between border-b pb-4">
                    <h2 class="text-xl font-bold text-[#230C31]">
                        {{ $sectionId ? 'Editar sección: '.$title : 'Nueva sección' }}
                    </h2>
                    @if ($sectionId)
                        <button type="button" 
                                wire:click="delete({{ $sectionId }})" 
                                wire:confirm="¿Estás seguro de que deseas eliminar esta sección definitivamente?"
                                class="text-xs font-semibold text-red-600 hover:text-red-800 hover:underline">
                            Eliminar esta sección
                        </button>
                    @endif
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Tipo de sección
                        <select wire:model.live="type" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none">
                            <option value="hero">Carrusel principal automático</option>
                            <option value="showcase">Vitrina destacada (Foto grande + cuadrícula de productos/colores)</option>
                            <option value="split">Presentación destacada (Mitad texto y mitad imagen)</option>
                            <option value="products">Portada y productos</option>
                            <option value="banner">Banner de campaña completo</option>
                            <option value="video">Galería de videos (hasta 3 con transición continua)</option>
                            <option value="categories">Cuadrícula de categorías</option>
                            <option value="lines">Líneas / colecciones</option>
                        </select>
                    </label>

                    <label class="block text-sm font-semibold text-gray-700">
                        Orden de aparición (número)
                        <input type="number" min="0" max="10000" wire:model="sortOrder" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none">
                    </label>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-semibold text-gray-700 sm:col-span-2">
                        Título de la sección
                        <input wire:model="title" maxlength="200" required placeholder="Ej: Innovación textil para el sector salud" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none">
                    </label>
                </div>

                <label class="block text-sm font-semibold text-gray-700">
                    Descripción o texto descriptivo
                    <textarea wire:model="description" rows="4" maxlength="2000" placeholder="Párrafos explicativos sobre los uniformes, beneficios, tecnología de telas, etc." class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none"></textarea>
                </label>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is-active" wire:model="isActive" class="h-4 w-4 rounded border-gray-300 text-[#230C31] focus:ring-purple-500">
                    <label for="is-active" class="text-sm font-medium text-gray-700">Mostrar esta sección en la tienda</label>
                </div>

                <!-- SECCIÓN SPLIT (MITAD TEXTO / MITAD IMAGEN) -->
                @if ($type === 'split')
                    <fieldset class="space-y-4 rounded-xl border border-purple-200 bg-purple-50/50 p-5">
                        <legend class="text-base font-bold text-[#230C31]">Configuración: Mitad texto y mitad imagen</legend>
                        <p class="text-xs text-gray-600">Presenta una fotografía de alta calidad a un lado y a la par el contenido editorial con un banner de texto destacado (cápsula).</p>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Posición de la imagen
                                <select wire:model="imagePosition" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white p-3 text-sm focus:border-purple-600 focus:outline-none">
                                    <option value="left">Imagen a la izquierda, texto a la derecha</option>
                                    <option value="right">Texto a la izquierda, imagen a la derecha</option>
                                </select>
                            </label>

                            <label class="block text-sm font-semibold text-gray-700">
                                Banner o cápsula destacada inferior
                                <input wire:model="highlightText" maxlength="500" placeholder="Ej: ¡SOMOS INTELIGENCIA TEXTIL QUE PROTEGE LA VIDA!" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white p-3 text-sm focus:border-purple-600 focus:outline-none">
                            </label>
                        </div>
                    </fieldset>
                @endif

                <!-- SECCIÓN SHOWCASE (VITRINA FOTO GRANDE + CUADRÍCULA DE PRODUCTOS) -->
                @if ($type === 'showcase')
                    <fieldset class="space-y-4 rounded-xl border border-purple-200 bg-purple-50/50 p-5">
                        <legend class="text-base font-bold text-[#230C31]">Configuración: Vitrina destacada (Foto grande + Productos/Colores)</legend>
                        <p class="text-xs text-gray-600">Muestra una foto vertical de muestra destacada y a su lado una cuadrícula con productos, sus muestras de color interactivas, precios y botones de selección.</p>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block text-sm font-semibold text-gray-700">
                                Posición de la foto grande
                                <select wire:model="imagePosition" class="mt-1 block w-full rounded-xl border border-gray-300 bg-white p-3 text-sm focus:border-purple-600 focus:outline-none">
                                    <option value="right">Foto grande a la derecha (productos a la izquierda)</option>
                                    <option value="left">Foto grande a la izquierda (productos a la derecha)</option>
                                </select>
                            </label>
                        </div>
                    </fieldset>
                @endif

                <!-- CARRUSEL HERO -->
                @if ($type === 'hero')
                    <fieldset class="space-y-4">
                        <legend class="text-base font-bold text-[#230C31]">Carrusel principal · Hasta 4 imágenes</legend>
                        <p class="text-xs text-gray-500">Con dos o más imágenes, avanzan automáticamente en orden cada 6 segundos. Usa imágenes horizontales de proporciones similares.</p>
                        
                        <div class="grid gap-4 sm:grid-cols-2">
                            @for ($slideIndex = 0; $slideIndex < 4; $slideIndex++)
                                <div wire:key="slide-slot-{{ $sectionId }}-{{ $slideIndex }}" class="space-y-3 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <h3 class="text-sm font-bold text-[#230C31]">Imagen {{ $slideIndex + 1 }}</h3>
                                    @if ($slidePaths[$slideIndex] ?? '')
                                        <img src="{{ Storage::disk('public')->url($slidePaths[$slideIndex]) }}" alt="Vista previa {{ $slideIndex + 1 }}" class="h-32 w-full rounded-lg object-cover">
                                    @elseif ($slideAssets[$slideIndex] ?? '')
                                        <img src="{{ asset($slideAssets[$slideIndex]) }}" alt="Vista previa {{ $slideIndex + 1 }}" class="h-32 w-full rounded-lg object-cover">
                                    @endif

                                    <label class="block text-xs font-semibold text-gray-600">
                                        Elegir de la biblioteca
                                        <select wire:model.live="slideAssets.{{ $slideIndex }}" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white p-2 text-xs">
                                            <option value="">Sin imagen de biblioteca</option>
                                            @foreach ($assets as $path => $name)
                                                <option value="{{ $path }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </label>

                                    <label class="block text-xs font-semibold text-gray-600">
                                        O subir archivo
                                        <input type="file" wire:model="slideUploads.{{ $slideIndex }}" accept="image/png,image/jpeg,image/webp" class="mt-1 block w-full text-xs">
                                    </label>
                                    <p wire:loading wire:target="slideUploads.{{ $slideIndex }}" class="text-xs text-purple-600">Subiendo imagen…</p>
                                    @error('slideUploads.'.$slideIndex) <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                                    <label class="block text-xs font-semibold text-gray-600">
                                        Texto descriptivo (alt)
                                        <input wire:model="slideAlts.{{ $slideIndex }}" maxlength="255" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white p-2 text-xs">
                                    </label>

                                    <label class="block text-xs font-semibold text-gray-600">
                                        Ruta o categoría al hacer clic (ej: /catalogo/odontopediatria)
                                        <input wire:model="slideUrls.{{ $slideIndex }}" placeholder="/catalogo/odontopediatria" class="mt-1 block w-full rounded-lg border border-gray-300 bg-white p-2 text-xs">
                                    </label>

                                    <label class="flex items-center gap-2 text-xs text-gray-600">
                                        <input type="checkbox" wire:model="removeSlides.{{ $slideIndex }}" class="rounded text-[#230C31]"> Quitar esta imagen al guardar
                                    </label>
                                </div>
                            @endfor
                        </div>
                    </fieldset>
                @endif

                <!-- IMÁGENES PARA BANNER, PRODUCTS, SPLIT, SHOWCASE O POSTER DE VIDEO -->
                @if (in_array($type, ['products', 'banner', 'video', 'split', 'showcase']))
                    <fieldset class="space-y-4 rounded-xl border border-gray-200 p-5">
                        <legend class="text-sm font-bold text-gray-700">
                            {{ $type === 'video' ? 'Imagen de portada del video (poster)' : ($type === 'showcase' ? 'Foto de muestra grande (Modelo o prenda destacada)' : 'Imagen de la sección') }}
                        </legend>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <label class="block text-xs font-semibold text-gray-600">
                                Seleccionar de la biblioteca
                                <select wire:model="asset" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none">
                                    <option value="">Sin imagen de biblioteca</option>
                                    @foreach ($assets as $path => $name)
                                        <option value="{{ $path }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block text-xs font-semibold text-gray-600">
                                O subir una imagen nueva (JPG, PNG, WebP)
                                <input type="file" wire:model="image" accept="image/png,image/jpeg,image/webp" class="mt-1 block w-full text-xs">
                                <span class="text-[11px] text-gray-400">Máximo 6 MB. La subida tiene prioridad.</span>
                            </label>
                        </div>

                        <p wire:loading wire:target="image" class="text-xs text-purple-600">Subiendo imagen…</p>

                        @if ($selectedSection?->imageUrl())
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 mb-1">Imagen actual:</p>
                                <img src="{{ $selectedSection->imageUrl() }}" alt="Vista previa actual" class="max-h-48 rounded-xl object-contain border">
                            </div>
                        @endif

                        @if ($selectedSection?->image_path)
                            <label class="flex items-center gap-2 text-xs text-gray-600">
                                <input type="checkbox" wire:model="removeImage" class="rounded text-[#230C31]"> Quitar imagen subida y usar biblioteca
                            </label>
                        @endif

                        <label class="block text-xs font-semibold text-gray-600">
                            Texto alternativo de la imagen
                            <input wire:model="imageAlt" maxlength="255" class="mt-1 block w-full rounded-xl border border-gray-300 p-2.5 text-sm focus:border-purple-600 focus:outline-none">
                        </label>

                        @if (in_array($type, ['products', 'banner']))
                            <label class="flex items-center gap-2 text-xs text-gray-600">
                                <input type="checkbox" wire:model="imageOnly" class="rounded text-[#230C31]"> La imagen ya contiene el texto (mostrarla completa)
                            </label>
                        @endif
                    </fieldset>
                @endif

                <!-- GESTIÓN DE VIDEOS (HASTA 3 VIDEOS CON REPRODUCCIÓN CONTINUA) -->
                @if ($type === 'video')
                    <fieldset class="space-y-4 rounded-xl border border-purple-200 bg-purple-50/40 p-5">
                        <legend class="text-base font-bold text-[#230C31]">Videos de la sección · Hasta 3 videos continuos</legend>
                        <p class="text-xs text-gray-600">Puedes subir videos directos en formato MP4 o WebM (hasta 40 MB cada uno) o ingresar direcciones HTTPS. Si hay 2 o 3 videos, se reproducen uno tras otro con una transición fluida.</p>

                        <div class="space-y-4">
                            @for ($vIdx = 0; $vIdx < 3; $vIdx++)
                                <div wire:key="video-slot-{{ $vIdx }}" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-bold text-[#230C31]">Video {{ $vIdx + 1 }}</h3>
                                        @if (!empty($videoPaths[$vIdx]) || !empty($videoUrls[$vIdx]))
                                            <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-[11px] font-semibold text-green-800">Cargado</span>
                                        @else
                                            <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-[11px] font-medium text-gray-600">Disponible</span>
                                        @endif
                                    </div>

                                    @if (!empty($videoPaths[$vIdx]))
                                        <div class="rounded-lg bg-black/5 p-2">
                                            <video src="{{ Storage::disk('public')->url($videoPaths[$vIdx]) }}" class="max-h-36 w-full rounded object-cover" controls preload="metadata"></video>
                                        </div>
                                    @elseif (!empty($videoUrls[$vIdx]))
                                        <p class="text-xs text-gray-500 truncate font-mono bg-gray-100 p-2 rounded">
                                            {{ $videoUrls[$vIdx] }}
                                        </p>
                                    @endif

                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <label class="block text-xs font-semibold text-gray-600">
                                            Subir archivo de video (MP4 o WebM, máx 40MB)
                                            <input type="file" wire:model="videoUploads.{{ $vIdx }}" accept="video/mp4,video/webm" class="mt-1 block w-full text-xs">
                                        </label>

                                        <label class="block text-xs font-semibold text-gray-600">
                                            O dirección HTTPS del video
                                            <input type="url" wire:model="videoUrls.{{ $vIdx }}" placeholder="https://.../video.mp4" class="mt-1 block w-full rounded-lg border border-gray-300 p-2 text-xs">
                                        </label>
                                    </div>

                                    <p wire:loading wire:target="videoUploads.{{ $vIdx }}" class="text-xs text-purple-600">Subiendo video {{ $vIdx + 1 }}…</p>
                                    @error('videoUploads.'.$vIdx) <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                                    <label class="block text-xs font-semibold text-gray-600">
                                        Título o etiqueta del video
                                        <input wire:model="videoTitles.{{ $vIdx }}" maxlength="200" placeholder="Ej: Demostración antifluidos" class="mt-1 block w-full rounded-lg border border-gray-300 p-2 text-xs">
                                    </label>

                                    @if (!empty($videoPaths[$vIdx]) || !empty($videoUrls[$vIdx]))
                                        <label class="flex items-center gap-2 text-xs text-red-600 font-medium">
                                            <input type="checkbox" wire:model="removeVideos.{{ $vIdx }}" class="rounded text-red-600"> Eliminar este video al guardar
                                        </label>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </fieldset>
                @endif

                <!-- ENLACE Y BOTÓN DE ACCIÓN -->
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-sm font-semibold text-gray-700">
                        Texto del enlace o botón
                        <input wire:model="buttonText" maxlength="100" placeholder="Ej: Explorar catálogo" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none">
                    </label>

                    <label class="block text-sm font-semibold text-gray-700">
                        Destino del botón dentro de la tienda
                        <input wire:model="buttonUrl" placeholder="/catalogo" required class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none">
                    </label>
                </div>

                <!-- PRODUCTOS ASOCIADOS (PARA SECCIÓN PRODUCTS Y SHOWCASE) -->
                @if (in_array($type, ['products', 'showcase']))
                    <fieldset class="rounded-xl border border-gray-200 p-5">
                        <legend class="text-sm font-bold text-gray-700">
                            {{ $type === 'showcase' ? 'Productos para la cuadrícula de la vitrina (se recomiendan de 2 a 4)' : 'Productos de esta sección (máximo 16)' }}
                        </legend>
                        <p class="my-2 text-xs text-gray-500">
                            {{ $type === 'showcase' ? 'Acompañarán a la foto grande en una cuadrícula con sus colores interactivos y botón de selección.' : 'Aparecen en la cuadrícula de esta colección. Los productos desactivados se ocultan automáticamente.' }}
                        </p>
                        <div class="max-h-72 space-y-2 overflow-y-auto rounded-lg border border-gray-200 p-4">
                            @forelse ($products as $product)
                                <label class="flex items-center gap-3 text-sm text-gray-700 hover:text-black">
                                    <input type="checkbox" wire:model="productIds" value="{{ $product->id }}" class="rounded text-[#230C31]">
                                    <span>{{ $product->name }}{{ $product->is_active ? '' : ' · (Inactivo)' }}</span>
                                </label>
                            @empty
                                <p class="text-xs text-gray-500">Crea productos primero desde el catálogo administrativo.</p>
                            @endforelse
                        </div>
                    </fieldset>
                @endif

                <div class="flex flex-wrap items-center justify-between gap-4 border-t pt-5">
                    <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-[#230C31] px-7 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#3d1852] disabled:opacity-50">
                        <span wire:loading.remove wire:target="save,image,slideUploads,videoUploads">Guardar sección</span>
                        <span wire:loading wire:target="save,image,slideUploads,videoUploads">Guardando cambios…</span>
                    </button>

                    @if ($sectionId)
                        <button type="button" 
                                wire:click="delete({{ $sectionId }})" 
                                wire:confirm="¿Estás seguro de que deseas eliminar definitivamente la sección '{{ $title }}'?" 
                                class="rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                            Eliminar sección
                        </button>
                    @endif
                </div>
            </form>

            <!-- CABECERA Y PIE DE PÁGINA -->
            <form wire:submit="saveSite" class="space-y-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="text-xl font-bold text-[#230C31]">Cabecera y pie de página</h2>
                <p class="text-sm text-gray-600">Nombre, WhatsApp, correo y dirección se editan en <a class="underline text-[#563369]" href="{{ route('admin.settings.index') }}">Configuración de la tienda</a>.</p>

                <label class="block text-sm font-semibold text-gray-700">
                    Anuncio superior (cintillo)
                    <input wire:model="site.announcement" maxlength="200" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none">
                </label>

                <label class="block text-sm font-semibold text-gray-700">
                    Logotipo
                    <select wire:model="site.logo" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none">
                        @foreach ($assets as $path => $name)
                            <option value="{{ $path }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-semibold text-gray-700">
                    Descripción del pie de página
                    <textarea wire:model="site.footer_text" rows="3" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none"></textarea>
                </label>

                <label class="block text-sm font-semibold text-gray-700">
                    Horario de atención
                    <textarea wire:model="site.hours" rows="2" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-purple-600 focus:outline-none"></textarea>
                </label>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook', 'privacy' => 'Política de privacidad', 'terms' => 'Términos y condiciones'] as $key => $label)
                        <label class="block text-xs font-semibold text-gray-600">
                            {{ $label }} (enlace HTTPS opcional)
                            <input type="url" wire:model="site.{{ $key }}" class="mt-1 block w-full rounded-lg border border-gray-300 p-2.5 text-xs focus:border-purple-600 focus:outline-none">
                        </label>
                    @endforeach
                </div>

                <button type="submit" wire:loading.attr="disabled" class="rounded-xl bg-[#230C31] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#3d1852] disabled:opacity-50">
                    Guardar cabecera y pie
                </button>
            </form>
        </div>
    </div>
</div>
