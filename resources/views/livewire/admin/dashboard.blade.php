
<div class="space-y-8">

    <!-- =====================================
         ENCABEZADO DEL DASHBOARD
    ====================================== -->
    <section class="flex flex-col gap-4
                    sm:flex-row sm:items-end
                    sm:justify-between">

        <div>
            <div class="mb-3 flex items-center gap-2">

                <span class="h-2 w-2 rounded-full
                             bg-[#563369]"></span>

                <span class="text-[11px] font-bold
                             uppercase tracking-[0.18em]
                             text-[#563369]">
                    Panel de administración
                </span>
            </div>

            <h1 class="text-3xl font-semibold
                       tracking-tight text-[#230C31]
                       sm:text-4xl">
                Dashboard
            </h1>

            <p class="mt-3 text-sm leading-6
                      text-[#716779]">
                Bienvenido,
                <span class="font-semibold text-[#331344]">
                    {{ auth()->user()->name }}
                </span>.
                Aquí tienes un resumen de tu tienda.
            </p>
        </div>

        <div class="self-start rounded-full
                    border border-[#E3DCE7]
                    bg-white px-4 py-2
                    text-xs font-medium text-[#563369]">

            VK Uniformes · Administración
        </div>
    </section>

    <!-- =====================================
         TARJETAS ESTADÍSTICAS
    ====================================== -->
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:gap-5">

        <!-- TOTAL CATEGORÍAS -->
        <article class="relative overflow-hidden
                        rounded-2xl border border-[#EAEBEE]
                        bg-white p-5 shadow-sm
                        transition duration-300
                        hover:-translate-y-1
                        hover:shadow-lg">

            <div class="absolute left-0 top-0
                        h-1 w-full bg-[#331344]">
            </div>

            <div class="flex items-center justify-between">
                <p class="text-xs font-medium
                          text-[#716779] sm:text-sm">
                    Categorías
                </p>

                <span class="flex h-9 w-9 items-center
                             justify-center rounded-xl
                             bg-[#F0EAF3] text-[#331344]">
                    ◈
                </span>
            </div>

            <p class="mt-5 text-3xl font-bold
                      tracking-tight text-[#230C31]
                      sm:text-4xl">
                {{ $totalCategories }}
            </p>

            <p class="mt-2 text-xs text-[#817689]">
                Registradas
            </p>
        </article>

        <!-- CATEGORÍAS ACTIVAS -->
        <article class="relative overflow-hidden
                        rounded-2xl border border-[#EAEBEE]
                        bg-white p-5 shadow-sm
                        transition duration-300
                        hover:-translate-y-1
                        hover:shadow-lg">

            <div class="absolute left-0 top-0
                        h-1 w-full bg-[#9998A6]">
            </div>

            <div class="flex items-center justify-between">
                <p class="text-xs font-medium
                          text-[#716779] sm:text-sm">
                    Categorías activas
                </p>

                <span class="flex h-9 w-9 items-center
                             justify-center rounded-xl
                             bg-[#F0EAF3] text-[#331344]">
                    ✓
                </span>
            </div>

            <p class="mt-5 text-3xl font-bold
                      tracking-tight text-[#331344]
                      sm:text-4xl">
                {{ $activeCategories }}
            </p>

            <p class="mt-2 text-xs text-[#817689]">
                Visibles en la tienda
            </p>
        </article>

        <!-- PRODUCTOS -->
        <article class="relative overflow-hidden
                        rounded-2xl border border-[#EAEBEE]
                        bg-white p-5 shadow-sm
                        transition duration-300
                        hover:-translate-y-1
                        hover:shadow-lg">

            <div class="absolute left-0 top-0
                        h-1 w-full bg-[#D1CAD7]">
            </div>

            <div class="flex items-center justify-between">
                <p class="text-xs font-medium
                          text-[#716779] sm:text-sm">
                    Productos
                </p>

                <span class="flex h-9 w-9 items-center
                             justify-center rounded-xl
                             bg-[#F8F7FA] text-[#563369]">
                    ◇
                </span>
            </div>

            <p class="mt-5 text-sm font-semibold
                      text-[#331344] sm:text-lg">
                Próximamente
            </p>

            <p class="mt-3 text-xs text-[#817689]">
                Módulo pendiente
            </p>
        </article>



    </section>

    <!-- =====================================
         CONTENIDO INFORMATIVO
    ====================================== -->
    <section class="grid gap-5 lg:grid-cols-3">

        <!-- CONFIGURACIÓN DE LA TIENDA -->
        <article class="rounded-2xl
                        border border-[#EAEBEE]
                        bg-white p-6 shadow-sm
                        lg:col-span-2">

            <div class="flex items-center gap-3">

                <div class="h-10 w-1 rounded-full
                            bg-[#331344]">
                </div>

                <div>
                    <h2 class="text-lg font-semibold
                               text-[#230C31]">
                        Configuración de la tienda
                    </h2>

                    <p class="mt-1 text-xs text-[#817689]">
                        Estado general del sistema
                    </p>
                </div>
            </div>

            <p class="mt-6 text-sm leading-7
                      text-[#716779]">
                El panel administrativo está preparado
                para gestionar el catálogo de VK Uniformes.

                Los próximos módulos permitirán
                administrar categorías, productos,
                fotografías, precios, variantes
                y contenido de la tienda.
            </p>

            <!-- ESTADO CATEGORÍAS -->
            <div class="mt-7 rounded-xl
                        border border-[#E7DDEA]
                        bg-[#F8F4FA] p-5">

                <div class="flex items-start gap-3">

                    <div class="flex h-9 w-9 shrink-0
                                items-center justify-center
                                rounded-full bg-[#331344]
                                text-sm font-bold text-white">
                        ✓
                    </div>

                    <div>
                        <p class="text-sm font-semibold
                                  text-[#331344]">
                            Categorías conectadas a MySQL
                        </p>

                        <p class="mt-2 text-xs leading-6
                                  text-[#716779]">
                            Actualmente tienes
                            <strong>{{ $totalCategories }}</strong>
                            categorías registradas y
                            <strong>{{ $activeCategories }}</strong>
                            categorías activas.
                        </p>
                    </div>
                </div>
            </div>
        </article>

        <!-- SIGUIENTE MÓDULO -->
        <article class="relative overflow-hidden
                        rounded-2xl bg-[#331344]
                        p-6 text-white shadow-sm">

            <div class="absolute -right-14 -top-14
                        h-44 w-44 rounded-full
                        bg-white/5">
            </div>

            <div class="relative z-10">

                <span class="inline-flex rounded-full
                             border border-white/20
                             bg-white/10 px-3 py-1.5
                             text-[10px] font-semibold
                             uppercase tracking-widest
                             text-white/80">
                    Siguiente etapa
                </span>

                <h2 class="mt-6 text-xl font-semibold">
                    Gestión de categorías
                </h2>

                <p class="mt-4 text-sm leading-7
                          text-white/75">
                    Pronto podrás crear y editar
                    categorías, subir fotografías,
                    modificar su orden y controlar
                    cuáles aparecen en tu tienda.
                </p>

                <div class="mt-8 h-px w-full
                            bg-white/20"></div>

                <p class="mt-4 text-xs text-white/70">
                    VK Uniformes · Desarrollo modular
                </p>
            </div>
        </article>
    </section>

</div>
