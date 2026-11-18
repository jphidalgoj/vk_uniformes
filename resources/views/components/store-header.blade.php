
<header class="store-header sticky top-0 z-50
               border-b border-[#EAEBEE]
               bg-white shadow-sm">

    <!-- =====================================
         FRANJA SUPERIOR
    ====================================== -->
    <div class="bg-[#230C31] px-4 py-2.5
                text-center">

        <p class="text-[10px] font-semibold
                  uppercase tracking-[0.18em]
                  text-[#E4D8EB] sm:text-xs">

            {{ $site['announcement'] ?? 'VK Uniformes · Colecciones profesionales' }}

        </p>
    </div>

    <!-- =====================================
         NAVEGACIÓN PRINCIPAL
    ====================================== -->
    <div class="mx-auto flex max-w-7xl
                flex-wrap items-center gap-3
                px-4 py-3 sm:px-6
                lg:gap-7 lg:px-8 lg:py-4">

        <!-- LOGOTIPO -->
        <a href="{{ route('home') }}"
           aria-label="VK Uniformes - Inicio"
           class="order-1 flex shrink-0
                  items-center gap-3">

            <img src="{{ asset($site['logo'] ?? 'images/logo-vk.png') }}" alt="{{ $businessName }}" width="170" height="70" class="store-logo">
        </a>

        <!-- =================================
             MENÚ PARA CELULARES Y TABLETS
        ================================== -->
        <details class="relative order-2 ml-auto lg:hidden">

            <summary
                aria-label="Abrir menú de navegación"
                class="flex h-11 w-11
                       cursor-pointer list-none
                       items-center justify-center
                       rounded-xl border
                       border-[#E3DCE7]
                       bg-[#F8F7FA]
                       text-[#331344]
                       [&::-webkit-details-marker]:hidden">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="23"
                    height="23"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true">

                    <path d="M4 6h16"/>
                    <path d="M4 12h16"/>
                    <path d="M4 18h16"/>
                </svg>
            </summary>

            <!-- PANEL DESPLEGABLE -->
            <nav
                aria-label="Menú móvil"
                class="absolute right-0 top-full
                       mt-3 max-h-[75vh]
                       w-[min(88vw,340px)]
                       overflow-y-auto
                       rounded-2xl border
                       border-[#E3DCE7]
                       bg-white p-4
                       shadow-2xl">

                <div class="space-y-1">

                    <a href="{{ route('home') }}"
                       class="flex min-h-11
                              items-center rounded-xl
                              px-4 text-sm font-medium
                              transition
                              {{ request()->routeIs('home')
                                 ? 'bg-[#331344] text-white'
                                 : 'text-[#331344] hover:bg-[#F8F7FA]' }}">
                        Inicio
                    </a>

                    <a href="{{ route('store.catalog') }}"
                       class="flex min-h-11
                              items-center rounded-xl
                              px-4 text-sm font-medium
                              transition
                              {{ request()->routeIs('store.catalog')
                                 ? 'bg-[#331344] text-white'
                                 : 'text-[#331344] hover:bg-[#F8F7FA]' }}">
                        Ver todo el catálogo
                    </a>
                </div>

                <div class="my-4 h-px
                            bg-[#EAEBEE]"></div>

                <p class="mb-3 px-4 text-[10px]
                          font-bold uppercase
                          tracking-[0.18em]
                          text-[#817689]">
                    Categorías
                </p>

                <div class="space-y-1">

                    @forelse ($categories as $category)

                        @php
                            $isCurrent =
                                request()->routeIs('store.category')
                                && request()->route('categorySlug') === $category->slug;
                        @endphp

                        <a
                            href="{{ route('store.category', $category->slug) }}"
                            class="flex min-h-11
                                   items-center justify-between
                                   gap-3 rounded-xl
                                   px-4 text-sm transition
                                   {{ $isCurrent
                                      ? 'bg-[#F1E9F5] font-semibold text-[#331344]'
                                      : 'text-[#716779] hover:bg-[#F8F7FA] hover:text-[#331344]' }}">

                            <span>{{ $category->name }}</span>

                            <span aria-hidden="true">›</span>
                        </a>

                    @empty

                        <p class="px-4 py-3 text-xs
                                  text-[#817689]">
                            Colecciones próximamente.
                        </p>

                    @endforelse
                </div>
            </nav>
        </details>

        <!-- =================================
             BUSCADOR FUNCIONAL
        ================================== -->
        <form
            action="{{ route('store.catalog') }}"
            method="GET"
            role="search"
            class="order-3 w-full
                   lg:order-2 lg:ml-auto
                   lg:max-w-xl lg:flex-1">

            <div class="flex min-h-12
                        items-stretch overflow-hidden
                        rounded-full border
                        border-[#E3DCE7]
                        bg-[#F8F7FA]
                        transition
                        focus-within:border-[#563369]
                        focus-within:ring-2
                        focus-within:ring-[#563369]/10">

                <label for="store-search" class="sr-only">
                    Buscar productos
                </label>

                <input
                    id="store-search"
                    name="q"
                    type="search"
                    maxlength="120"
                    value="{{ is_string(request()->query('q')) ? request()->query('q') : '' }}"
                    placeholder="Buscar scrubs, batas, gorritos..."
                    class="min-w-0 flex-1
                           bg-transparent px-5
                           text-sm text-[#230C31]
                           outline-none
                           placeholder:text-[#91879A]"
                >

                <button
                    type="submit"
                    aria-label="Buscar productos"
                    class="flex w-13 shrink-0
                           items-center justify-center
                           bg-[#331344]
                           text-white transition
                           hover:bg-[#563369]">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true">

                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- =====================================
         NAVEGACIÓN DE ESCRITORIO
    ====================================== -->
    <div class="hidden border-t
                border-[#F0ECF2] bg-white lg:block">

        <nav
            aria-label="Navegación principal"
            class="mx-auto flex max-w-7xl
                   items-center gap-8
                   overflow-x-auto
                   whitespace-nowrap
                   px-8 py-4">

            <!-- INICIO -->
            <a
                href="{{ route('home') }}"
                class="shrink-0 text-sm font-semibold
                       transition
                       {{ request()->routeIs('home')
                          ? 'text-[#331344]'
                          : 'text-[#716779] hover:text-[#331344]' }}">

                Inicio
            </a>

            <!-- CATÁLOGO -->
            <a
                href="{{ route('store.catalog') }}"
                class="shrink-0 text-sm font-semibold
                       transition
                       {{ request()->routeIs('store.catalog')
                          ? 'text-[#331344]'
                          : 'text-[#716779] hover:text-[#331344]' }}">

                Catálogo
            </a>

            <!-- CATEGORÍAS DE MYSQL -->
            @foreach ($categories as $category)

                @php
                    $isCurrent =
                        request()->routeIs('store.category')
                        && request()->route('categorySlug') === $category->slug;
                @endphp

                <a
                    href="{{ route('store.category', $category->slug) }}"
                    class="shrink-0 text-sm
                           font-medium transition
                           {{ $isCurrent
                              ? 'font-semibold text-[#331344]'
                              : 'text-[#716779] hover:text-[#331344]' }}">

                    {{ $category->name }}
                </a>

            @endforeach
        </nav>
    </div>
</header>
