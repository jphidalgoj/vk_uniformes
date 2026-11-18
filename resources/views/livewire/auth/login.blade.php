
<div class="min-h-screen bg-vk-background lg:grid lg:grid-cols-2">

    <!-- PRESENTACIÓN DE LA MARCA -->
    <section class="relative hidden overflow-hidden
                    bg-vk-dark p-12 text-white lg:flex
                    lg:flex-col lg:justify-between">

        <div class="absolute -right-32 top-24 h-96 w-96
                    rounded-full bg-vk-accent/30 blur-3xl"></div>

        <a href="{{ route('home') }}"
           class="relative z-10 flex items-center gap-4">

            <img src="{{ asset('images/logo-vk.png') }}"
                 alt="Logo VK Uniformes"
                 class="h-16 w-16 rounded-xl object-contain bg-white/10 p-1">

            <span class="text-xl font-semibold tracking-wide">
                VK UNIFORMES
            </span>
        </a>

        <div class="relative z-10 max-w-xl">
            <span class="text-xs font-semibold uppercase
                         tracking-[0.3em] text-vk-silver">
                Administración empresarial
            </span>

            <h1 class="mt-6 text-5xl font-semibold leading-tight">
                Tu negocio,
                <span class="text-white/65">
                    bajo control.
                </span>
            </h1>

            <p class="mt-6 max-w-md text-lg leading-8
                      text-white/65">
                Administra tus colecciones, productos
                y pedidos desde un solo lugar.
            </p>

            <div class="mt-10 h-px w-32 bg-vk-silver/60"></div>
        </div>

        <p class="relative z-10 text-sm text-white/50">
            VK Uniformes · Panel de administración
        </p>
    </section>

    <!-- FORMULARIO DE ACCESO -->
    <section class="flex min-h-screen flex-col
                    bg-white px-5 py-8 sm:px-10
                    lg:justify-center">

        <a href="{{ route('home') }}"
           class="mb-14 flex items-center gap-3 lg:hidden">

            <img src="{{ asset('images/logo-vk.jpg') }}"
                 alt="VK Uniformes"
                 class="h-12 w-12 rounded-xl object-cover">

            <span class="font-semibold text-vk-primary">
                VK UNIFORMES
            </span>
        </a>

        <div class="mx-auto w-full max-w-md">

            <span class="inline-flex rounded-full
                         bg-vk-background px-4 py-2
                         text-xs font-semibold text-vk-primary">
                Acceso exclusivo
            </span>

            <h2 class="mt-6 text-3xl font-semibold
                       tracking-tight text-vk-dark">
                Bienvenido de nuevo
            </h2>

            <p class="mb-10 mt-3 text-sm leading-6
                      text-slate-500">
                Ingresa tus credenciales para acceder
                al panel administrativo.
            </p>

            <form wire:submit="login" class="space-y-6">

                <div>
                    <label for="email"
                           class="mb-2 block text-sm font-medium
                                  text-vk-text">
                        Correo electrónico
                    </label>

                    <input
                        id="email"
                        type="email"
                        wire:model="email"
                        autocomplete="username"
                        placeholder="correo@ejemplo.com"
                        required
                        class="h-13 w-full rounded-xl
                               border border-vk-light
                               bg-vk-background px-4
                               text-base text-vk-dark
                               outline-none transition
                               focus:border-vk-primary
                               focus:ring-2
                               focus:ring-vk-primary/15"
                    >

                    @error('email')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password"
                           class="mb-2 block text-sm font-medium
                                  text-vk-text">
                        Contraseña
                    </label>

                    <input
                        id="password"
                        type="password"
                        wire:model="password"
                        autocomplete="current-password"
                        placeholder="Tu contraseña"
                        required
                        class="h-13 w-full rounded-xl
                               border border-vk-light
                               bg-vk-background px-4
                               text-base text-vk-dark
                               outline-none transition
                               focus:border-vk-primary
                               focus:ring-2
                               focus:ring-vk-primary/15"
                    >

                    @error('password')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="login"
                    class="flex h-13 w-full items-center
                           justify-center rounded-xl
                           bg-vk-primary px-6
                           text-sm font-semibold text-white
                           transition duration-300
                           hover:bg-vk-accent
                           disabled:opacity-60">

                    <span wire:loading.remove wire:target="login">
                        Iniciar sesión
                    </span>

                    <span wire:loading wire:target="login">
                        Verificando...
                    </span>
                </button>
            </form>

            <div class="mt-10 border-t border-vk-light pt-6">
                <a href="{{ route('home') }}"
                   class="text-sm font-medium text-vk-primary
                          transition hover:text-vk-accent">
                    ← Volver a la tienda
                </a>
            </div>
        </div>
    </section>
</div>
