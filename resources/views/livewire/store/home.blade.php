<main class="store-home reference-home">
    <h1 class="sr-only">{{ $businessName }} · Uniformes profesionales</h1>
    @foreach ($sections as $section)
        @php
            $type = $section->content['type'] ?? 'hero';
            $imageUrl = $section->imageUrl();
            $sectionProducts = collect($section->content['product_ids'] ?? [])->map(fn ($id) => $products->get($id))->filter();
        @endphp
        <section id="{{ $section->key }}" wire:key="landing-{{ $section->id }}" class="landing-section {{ $type === 'hero' ? 'landing-hero' : 'landing-contained' }}" aria-label="{{ $section->title }}">
            @if ($type === 'hero' && count($section->slideImages()))
                <div class="vk-carousel" data-carousel role="region" aria-roledescription="carrusel" aria-label="{{ $section->title }}" tabindex="0">
                    <div class="vk-carousel-stage">
                        @foreach ($section->slideImages() as $slide)
                            <a href="{{ $slide['destination'] ?? $section->destination() }}" class="vk-slide {{ $loop->first ? 'is-active' : '' }}" data-slide aria-label="{{ $slide['alt'] }}" @if (!$loop->first) aria-hidden="true" tabindex="-1" inert @endif>
                                <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}" width="1672" height="941" loading="{{ $loop->first ? 'eager' : 'lazy' }}" @if ($loop->first) fetchpriority="high" @endif>
                            </a>
                        @endforeach
                    </div>
                    @if (count($section->slideImages()) > 1)
                        <div class="vk-carousel-toolbar" data-carousel-controls hidden>
                            <button type="button" data-previous aria-label="Imagen anterior">←</button>
                            <div class="vk-carousel-dots">@foreach ($section->slideImages() as $slide)<button type="button" data-go="{{ $loop->index }}" aria-label="Mostrar imagen {{ $loop->iteration }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"><span></span></button>@endforeach</div>
                            <button type="button" data-next aria-label="Imagen siguiente">→</button>
                            <button type="button" data-pause aria-label="Pausar carrusel" aria-pressed="false">Pausar</button>
                        </div>
                    @endif
                    <span class="sr-only" data-carousel-status aria-live="polite" aria-atomic="true"></span>
                </div>

            @elseif ($type === 'split')
                @php
                    $isImageRight = ($section->content['image_position'] ?? 'left') === 'right';
                    $highlightText = $section->content['highlight_text'] ?? '';
                @endphp
                <div class="landing-split mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-16">
                    <div class="grid items-center gap-8 lg:grid-cols-2 lg:gap-14 {{ $isImageRight ? 'lg:grid-flow-dense' : '' }}">
                        <div class="relative overflow-hidden rounded-3xl shadow-lg ring-1 ring-black/5 {{ $isImageRight ? 'lg:col-start-2' : '' }}">
                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $section->image_alt ?: $section->title }}" class="h-full w-full object-cover transition-transform duration-700 hover:scale-105" loading="lazy">
                            @else
                                <div class="flex aspect-[4/3] items-center justify-center bg-[#F1E9F5] text-3xl font-bold text-[#331344]">
                                    VK Uniformes
                                </div>
                            @endif
                        </div>

                        <div class="space-y-6 {{ $isImageRight ? 'lg:col-start-1' : '' }}">
                            @if ($section->eyebrow)
                                <span class="inline-block text-xs font-bold uppercase tracking-widest text-[#563369]">
                                    {{ $section->eyebrow }}
                                </span>
                            @endif

                            <h2 class="text-3xl font-bold tracking-tight text-[#230C31] sm:text-4xl lg:text-5xl leading-tight">
                                {{ $section->title }}
                            </h2>

                            @if ($section->description)
                                <div class="space-y-3 text-base text-gray-600 leading-relaxed font-normal">
                                    @foreach (explode("\n", $section->description) as $paragraph)
                                        @if (trim($paragraph))
                                            <p>{{ trim($paragraph) }}</p>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            @if ($highlightText)
                                <div class="landing-highlight-banner inline-block rounded-2xl bg-[#d5e3ec] px-6 py-4 text-[#1e3a5f] shadow-sm sm:rounded-r-full sm:rounded-l-2xl">
                                    <p class="text-base font-extrabold tracking-wide uppercase sm:text-lg">
                                        {{ $highlightText }}
                                    </p>
                                </div>
                            @endif

                            @if ($section->button_text)
                                <div class="pt-2">
                                    <a href="{{ $section->destination() }}" class="inline-flex items-center gap-2 rounded-xl bg-[#230C31] px-6 py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-[#3d1852] hover:shadow-lg">
                                        <span>{{ $section->button_text }}</span>
                                        <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            @elseif ($type === 'showcase')
                @php
                    $isImageRight = ($section->content['image_position'] ?? 'right') === 'right';
                @endphp
                <div class="landing-showcase mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-16">
                    @if ($section->title || $section->description)
                        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-gray-100 pb-5">
                            <div>
                                @if ($section->eyebrow)
                                    <span class="inline-block text-xs font-bold uppercase tracking-widest text-[#563369]">
                                        {{ $section->eyebrow }}
                                    </span>
                                @endif
                                <h2 class="text-2xl font-bold tracking-tight text-[#230C31] sm:text-3xl lg:text-4xl">
                                    {{ $section->title }}
                                </h2>
                                @if ($section->description)
                                    <p class="mt-2 text-sm sm:text-base text-gray-600 max-w-2xl leading-relaxed">
                                        {{ $section->description }}
                                    </p>
                                @endif
                            </div>
                            @if ($section->button_text)
                                <a href="{{ $section->destination() }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#563369] hover:text-[#230C31] transition">
                                    <span>{{ $section->button_text }}</span>
                                    <span aria-hidden="true">→</span>
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="grid items-stretch gap-6 lg:grid-cols-12 lg:gap-10 {{ $isImageRight ? 'lg:grid-flow-dense' : '' }}">
                        <!-- FOTO DE MUESTRA GRANDE (VERTICAL / MODELO DESTACADO) -->
                        <div class="group relative overflow-hidden rounded-3xl bg-gray-50 shadow-md ring-1 ring-black/5 lg:col-span-5 xl:col-span-5 {{ $isImageRight ? 'lg:col-start-8 xl:col-start-8' : 'lg:col-start-1' }} flex items-center justify-center min-h-[440px] lg:min-h-full">
                            @if ($imageUrl)
                                <a href="{{ $section->destination() }}" class="block h-full w-full overflow-hidden" aria-label="{{ $section->button_text ?: 'Ver todos los productos' }}">
                                    <img src="{{ $imageUrl }}" alt="{{ $section->image_alt ?: $section->title }}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-80 group-hover:opacity-90 transition-opacity"></div>
                                </a>

                                <!-- BOTÓN FLOTANTE SOBRE LA FOTO GRANDE -->
                                <div class="pointer-events-none absolute bottom-6 inset-x-0 flex justify-center px-4">
                                    <a href="{{ $section->destination() }}" class="pointer-events-auto inline-flex items-center gap-2 rounded-full bg-white/95 px-6 py-3 text-xs sm:text-sm font-bold tracking-wider uppercase text-[#230C31] shadow-xl backdrop-blur-md transition-all duration-300 hover:scale-105 hover:bg-white hover:shadow-2xl">
                                        <span>{{ $section->button_text ?: 'VER TODOS LOS PRODUCTOS' }}</span>
                                        <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            @else
                                <div class="flex aspect-[3/4] h-full w-full items-center justify-center bg-[#F1E9F5] p-8 text-center text-2xl font-bold text-[#331344]">
                                    VK Uniformes · Prenda Destacada
                                </div>
                            @endif
                        </div>

                        <!-- CUADRÍCULA DE PRODUCTOS / FOTOS / COLORES -->
                        <div class="lg:col-span-7 xl:col-span-7 {{ $isImageRight ? 'lg:col-start-1' : 'lg:col-start-6' }} flex flex-col justify-center">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6">
                                @forelse ($sectionProducts as $product)
                                    @include('livewire.store.partials.home-showcase-card', ['product' => $product])
                                @empty
                                    <div class="col-span-2 rounded-2xl border-2 border-dashed border-gray-200 p-8 text-center text-sm text-gray-500">
                                        Selecciona productos desde el panel administrativo para mostrarlos en esta vitrina.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            @elseif (in_array($type, ['banner', 'products']) && $imageUrl)
                <a href="{{ $section->destination() }}" class="landing-banner" aria-label="{{ $section->button_text ?: $section->title }}">
                    <img src="{{ $imageUrl }}" alt="{{ $section->image_alt ?: $section->title }}" loading="{{ $type === 'hero' ? 'eager' : 'lazy' }}" @if ($type === 'hero') fetchpriority="high" @endif>
                </a>
            @endif

            @if ($type === 'video')
                @php
                    $videos = $section->videos();
                @endphp
                @if (count($videos) > 0)
                    <div class="landing-video-showcase mx-auto max-w-6xl px-4 py-8 sm:px-6"
                         x-data="{
                             current: 0,
                             videos: @js($videos),
                             isPlaying: true,
                             nextVideo() {
                                 this.current = (this.current + 1) % this.videos.length;
                                 this.$nextTick(() => {
                                     const activeVideo = document.getElementById('vk-video-' + {{ $section->id }} + '-' + this.current);
                                     if (activeVideo) {
                                         activeVideo.currentTime = 0;
                                         activeVideo.muted = true;
                                         activeVideo.play().catch(() => {});
                                     }
                                 });
                             },
                             selectVideo(idx) {
                                 this.current = idx;
                                 this.$nextTick(() => {
                                     const activeVideo = document.getElementById('vk-video-' + {{ $section->id }} + '-' + this.current);
                                     if (activeVideo) {
                                         activeVideo.currentTime = 0;
                                         activeVideo.muted = true;
                                         activeVideo.play().catch(() => {});
                                     }
                                 });
                             },
                             togglePlay() {
                                 const activeVideo = document.getElementById('vk-video-' + {{ $section->id }} + '-' + this.current);
                                 if (!activeVideo) return;
                                 if (activeVideo.paused) {
                                     activeVideo.play();
                                     this.isPlaying = true;
                                 } else {
                                     activeVideo.pause();
                                     this.isPlaying = false;
                                 }
                             }
                         }"
                         x-init="$nextTick(() => {
                             const first = document.getElementById('vk-video-' + {{ $section->id }} + '-0');
                             if (first) {
                                 first.muted = true;
                                 first.defaultMuted = true;
                                 const playPromise = first.play();
                                 if (playPromise !== undefined) {
                                     playPromise.catch(() => {
                                         isPlaying = false;
                                     });
                                 }
                             }
                         })">
                        <div class="mb-4 text-center">
                            <h2 class="text-2xl font-bold text-[#230C31] sm:text-3xl">{{ $section->title }}</h2>
                            @if ($section->description)
                                <p class="mt-2 text-sm text-gray-600 max-w-2xl mx-auto">{{ $section->description }}</p>
                            @endif
                        </div>

                        <div class="relative aspect-video overflow-hidden rounded-3xl bg-black shadow-2xl">
                            @foreach ($videos as $idx => $v)
                                <video id="vk-video-{{ $section->id }}-{{ $idx }}"
                                       src="{{ $v['url'] }}"
                                       playsinline
                                       webkit-playsinline
                                       muted
                                       autoplay
                                       loop
                                       preload="auto"
                                       @if ($imageUrl) poster="{{ $imageUrl }}" @endif
                                       @ended="nextVideo()"
                                       class="absolute inset-0 h-full w-full object-cover transition-opacity duration-700 ease-in-out"
                                       :class="current === {{ $idx }} ? 'opacity-100 z-10' : 'opacity-0 pointer-events-none z-0'">
                                    Tu navegador no soporta video HTML5.
                                </video>
                            @endforeach

                            <!-- CONTROLES MINIMALISTAS (SIN TEXTOS MOLESTOS NI AUDIO) -->
                            <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 z-20 flex items-center justify-between pointer-events-none">
                                <button type="button" @click="togglePlay()" class="pointer-events-auto rounded-full bg-black/50 p-2.5 text-white backdrop-blur-md hover:bg-black/70 transition shadow-lg" :title="isPlaying ? 'Pausar' : 'Reproducir'">
                                    <span x-show="isPlaying" class="block w-4 h-4 text-xs font-bold leading-none">⏸</span>
                                    <span x-show="!isPlaying" class="block w-4 h-4 text-xs font-bold leading-none">▶</span>
                                </button>

                                @if (count($videos) > 1)
                                    <div class="pointer-events-auto flex items-center gap-1.5 rounded-full bg-black/40 px-3 py-1.5 backdrop-blur-md shadow-lg">
                                        @foreach ($videos as $idx => $v)
                                            <button type="button" 
                                                    @click="selectVideo({{ $idx }})"
                                                    class="h-2.5 rounded-full transition-all duration-300"
                                                    :class="current === {{ $idx }} ? 'w-6 bg-white' : 'w-2.5 bg-white/40 hover:bg-white/70'"
                                                    aria-label="Video {{ $idx + 1 }}">
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @elseif (in_array($type, ['categories', 'lines']))
                <div class="landing-heading"><h2>{{ $section->title }}</h2></div>
                <div class="{{ $type === 'lines' ? 'landing-lines' : 'store-category-grid' }}">
                    @forelse ($categories as $category)
                        <a class="store-category" href="{{ route('store.category', $category->slug) }}">
                            <div class="store-category-image">@if ($category->image_path)<img src="{{ Storage::disk('public')->url($category->image_path) }}" alt="{{ $category->name }}" loading="lazy">@else<span aria-hidden="true">VK</span>@endif</div>
                            <h3>{{ $category->name }}</h3>
                            @if ($type === 'lines')<span>Conoce más ↗</span>@endif
                        </a>
                    @empty<p class="store-empty">Las colecciones estarán disponibles próximamente.</p>@endforelse
                </div>
            @elseif ($type === 'products')
                <div class="landing-heading"><h2>{{ $section->title }}</h2><a href="{{ $section->destination() }}">{{ $section->button_text ?: 'Ver colección' }} <span aria-hidden="true">↗</span></a></div>
                @if ($section->description)<p class="landing-description">{{ $section->description }}</p>@endif
                <div x-data class="landing-products-wrap">
                    <div class="landing-products" x-ref="products">
                        @forelse ($sectionProducts as $product)
                            @include('livewire.store.partials.home-product-card', ['product' => $product, 'badgeText' => ''])
                        @empty<p class="store-empty">Próximamente encontrarás los productos de esta colección.</p>@endforelse
                    </div>
                    @if ($sectionProducts->count() > 2)
                        <div class="landing-controls"><button type="button" @click="$refs.products.scrollBy({left: -$refs.products.clientWidth, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'})" aria-label="Productos anteriores de {{ $section->title }}">←</button><button type="button" @click="$refs.products.scrollBy({left: $refs.products.clientWidth, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'})" aria-label="Productos siguientes de {{ $section->title }}">→</button></div>
                    @endif
                </div>
            @elseif (in_array($type, ['hero', 'banner']) && (($type === 'hero' ? !count($section->slideImages()) : !$imageUrl) || !($section->content['image_only'] ?? true)))
                <div class="landing-copy"><p class="store-eyebrow">{{ $section->eyebrow ?: $businessName }}</p><h2>{{ $section->title }}</h2>@if ($section->description)<p>{{ $section->description }}</p>@endif @if ($section->button_text)<a class="store-button" href="{{ $section->destination() }}">{{ $section->button_text }} ↗</a>@endif</div>
            @endif
        </section>
    @endforeach
</main>
