
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#230C31">

    <title>Administración | VK Uniformes</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @livewireStyles

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-[#F8F7FA]
             font-sans text-[#230C31] antialiased">

<div x-data="{ sidebarOpen: false }"
     @keydown.escape.window="sidebarOpen = false"
     class="min-h-screen">

    <!-- ========================================
         OVERLAY PARA MÓVILES
    ========================================= -->
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-[#16081F]/70
               backdrop-blur-sm lg:hidden"
        aria-hidden="true">
    </div>

    <!-- ========================================
         SIDEBAR ADMINISTRATIVO
    ========================================= -->
    <aside
        id="admin-sidebar"
        :class="sidebarOpen
            ? 'translate-x-0'
            : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50
               flex w-72 flex-col
               bg-[#230C31] text-white
               shadow-2xl
               transition-transform duration-300
               lg:translate-x-0 lg:shadow-none">

        <!-- LOGO Y MARCA -->
        <div class="flex h-22 shrink-0 items-center
                    justify-between
                    border-b border-white/10 px-5">

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3">

                @if (is_file(public_path('images/logo-vk.png')))
                    <img
                        src="{{ asset('images/logo-vk.png') }}"
                        alt="VK Uniformes"
                        width="48"
                        height="48"
                        class="h-12 w-12 rounded-xl
                               border border-white/15
                               object-contain bg-white/10 p-1"
                    >
                @elseif (is_file(public_path('images/logo-vk.jpg')))
                    <img
                        src="{{ asset('images/logo-vk.jpg') }}"
                        alt="VK Uniformes"
                        width="48"
                        height="48"
                        class="h-12 w-12 rounded-xl
                               border border-white/15
                               object-cover"
                    >
                @else
                    <div class="flex h-12 w-12 items-center
                                justify-center rounded-xl
                                bg-[#50305F] font-bold">
                        VK
                    </div>
                @endif

                <div>
                    <p class="text-sm font-bold tracking-wide">
                        VK UNIFORMES
                    </p>

                    <p class="mt-1 text-[10px]
                              uppercase tracking-widest
                              text-[#C9BDD2]">
                        Administración
                    </p>
                </div>
            </a>

            <button
                type="button"
                @click="sidebarOpen = false"
                aria-label="Cerrar menú"
                class="flex h-9 w-9 items-center
                       justify-center rounded-lg
                       text-white/70
                       hover:bg-white/10 lg:hidden">

                <span class="text-2xl">&times;</span>
            </button>
        </div>

        <!-- ====================================
             NAVEGACIÓN
        ===================================== -->
        <nav aria-label="Menú administrativo"
             class="flex-1 space-y-2
                    overflow-y-auto px-4 py-7">
            <a href="{{ route('admin.landing.index') }}" class="block rounded-lg bg-white/10 px-4 py-3 text-sm font-semibold hover:bg-white/20">Diseño de la portada</a>

            <p class="px-3 pb-3 text-[10px]
                      font-bold uppercase
                      tracking-[0.2em] text-[#AA98B5]">
                Principal
            </p>

            <!-- DASHBOARD ACTIVO -->
            <a href="{{ route('admin.dashboard') }}"
               aria-current="page"
               class="flex min-h-12 items-center
                      gap-3 rounded-xl
                      border border-white/10
                      bg-[#4B285E]
                      px-4 text-sm font-semibold
                      text-white shadow-sm
                      transition hover:bg-[#563369]">

                <svg xmlns="http://www.w3.org/2000/svg"
                     width="19" height="19"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <rect x="3" y="3"
                          width="7" height="9" rx="1"/>
                    <rect x="14" y="3"
                          width="7" height="5" rx="1"/>
                    <rect x="14" y="12"
                          width="7" height="9" rx="1"/>
                    <rect x="3" y="16"
                          width="7" height="5" rx="1"/>
                </svg>

                <span>Dashboard</span>
            </a>

            <!-- TÍTULO DEL GRUPO -->
            <p class="px-3 pb-3 pt-8 text-[10px]
                      font-bold uppercase
                      tracking-[0.2em] text-[#AA98B5]">
                Gestión de tienda
            </p>

            <!-- MÓDULOS PENDIENTES -->
            
<!-- CATEGORÍAS: MÓDULO ACTIVO -->
<a href="{{ route('admin.categories.index') }}"
   class="flex min-h-12 items-center gap-3
          rounded-xl px-4 text-sm font-medium
          transition
          {{ request()->routeIs('admin.categories.*')
                ? 'bg-[#4B285E] text-white'
                : 'text-[#C9BDD2] hover:bg-white/10 hover:text-white' }}">

    <span class="h-1.5 w-1.5 rounded-full
                 bg-[#9998A6]"></span>

    <span>Categorías</span>
</a>


<!-- MÓDULO PRODUCTOS -->
<a href="{{ route('admin.products.index') }}"
   class="flex min-h-12 items-center gap-3
          rounded-xl px-4 text-sm font-medium
          transition
          {{ request()->routeIs('admin.products.*')
                ? 'bg-[#4B285E] text-white'
                : 'text-[#C9BDD2] hover:bg-white/10 hover:text-white' }}">

    <span class="h-1.5 w-1.5 rounded-full
                 bg-[#9998A6]"></span>

    <span>Productos</span>
</a>


<!-- ADMINISTRAR BANNER PRINCIPAL -->
<a href="{{ route('admin.banners.home') }}"
   class="flex min-h-12 items-center gap-3
          rounded-xl px-4 text-sm font-medium
          transition
          {{ request()->routeIs('admin.banners.*')
              ? 'bg-[#4B285E] text-white'
              : 'text-[#C9BDD2] hover:bg-white/10 hover:text-white' }}">

    <span class="h-1.5 w-1.5 rounded-full
                 bg-[#9998A6]"></span>

    <span>Banner principal</span>
</a>


<!-- CONFIGURACIÓN DE LA TIENDA -->
<a href="{{ route('admin.settings.index') }}"
   class="flex min-h-12 items-center gap-3
          rounded-xl px-4 text-sm font-medium
          transition
          {{ request()->routeIs('admin.settings.*')
              ? 'bg-[#4B285E] text-white'
              : 'text-[#C9BDD2] hover:bg-white/10 hover:text-white' }}">

    <span class="h-1.5 w-1.5 rounded-full
                 bg-[#9998A6]"></span>

    <span>Configuración</span>
</a>




        </nav>

        <!-- ====================================
             PERFIL Y CERRAR SESIÓN
        ===================================== -->
        <div class="shrink-0 border-t
                    border-white/10 p-5">

            <div class="mb-5 flex items-center gap-3">

                <div class="flex h-11 w-11
                            items-center justify-center
                            rounded-full
                            border border-white/20
                            bg-[#563369]
                            font-semibold text-white">

                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold
                              text-white">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="mt-1 text-xs text-[#B9AEC3]">
                        Administrador
                    </p>
                </div>
            </div>

            <form method="POST"
                  action="{{ route('admin.logout') }}">
                @csrf

                <button
                    type="submit"
                    class="flex min-h-11 w-full
                           items-center justify-center
                           gap-2 rounded-xl
                           border border-white/20
                           bg-white/5 px-4
                           text-sm font-medium text-white
                           transition duration-300
                           hover:border-white/40
                           hover:bg-white/10">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="17" height="17"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <path d="M16 17l5-5-5-5"/>
                        <path d="M21 12H9"/>
                    </svg>

                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- ========================================
         CONTENEDOR PRINCIPAL
    ========================================= -->
    <div class="min-h-screen lg:pl-72">

        <!-- TOPBAR -->
        <header class="sticky top-0 z-30
                       flex h-19 items-center
                       justify-between
                       border-b border-[#EAEBEE]
                       bg-white/95
                       px-4 backdrop-blur-xl
                       sm:px-7 lg:px-9">

            <div class="flex items-center gap-4">

                <!-- MENÚ MÓVIL -->
                <button
                    type="button"
                    @click="sidebarOpen = true"
                    :aria-expanded="sidebarOpen.toString()"
                    aria-controls="admin-sidebar"
                    aria-label="Abrir menú"
                    class="flex h-11 w-11
                           items-center justify-center
                           rounded-xl
                           border border-[#EAEBEE]
                           bg-[#F8F7FA]
                           text-[#331344]
                           transition
                           hover:bg-[#F0EAF3]
                           lg:hidden">

                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="22" height="22"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round">
                        <path d="M4 6h16"/>
                        <path d="M4 12h16"/>
                        <path d="M4 18h16"/>
                    </svg>
                </button>

                <!-- TÍTULO -->
                <div>
                    <p class="text-sm font-bold
                              text-[#230C31]">
                        VK Uniformes
                    </p>

                    <p class="mt-1 text-[11px]
                              text-[#716779]">
                        Panel de administración
                    </p>
                </div>
            </div>

            <!-- VER TIENDA -->
            <a
                href="{{ route('home') }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex min-h-10
                       items-center justify-center
                       gap-2 rounded-xl
                       border border-[#E3DCE7]
                       bg-white px-4
                       text-xs font-semibold
                       text-[#331344]
                       transition duration-300
                       hover:border-[#331344]
                       hover:bg-[#F8F7FA]
                       sm:px-5 sm:text-sm">

                <svg xmlns="http://www.w3.org/2000/svg"
                     width="17" height="17"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     class="hidden sm:block">
                    <path d="M3 9l2-5h14l2 5"/>
                    <path d="M3 9v11h18V9"/>
                    <path d="M9 20v-7h6v7"/>
                </svg>

                Ver tienda
            </a>
        </header>

        <!-- CONTENIDO LIVEWIRE -->
        <main class="mx-auto max-w-7xl
                     px-4 py-7
                     sm:px-7 sm:py-9
                     lg:px-9">

            {{ $slot }}

        </main>

    </div>
</div>

@livewireScripts

</body>
</html>
