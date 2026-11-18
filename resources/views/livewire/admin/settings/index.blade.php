
<div class="mx-auto max-w-5xl space-y-7">

    <!-- ENCABEZADO -->
    <section>
        <span class="text-xs font-semibold
                     uppercase tracking-widest
                     text-[#563369]">
            Administración / Configuración
        </span>

        <h1 class="mt-2 text-3xl font-semibold
                   text-[#230C31]">
            Configuración de la tienda
        </h1>

        <p class="mt-2 text-sm leading-6
                  text-[#716779]">
            Administra la información comercial
            y los canales de contacto de VK Uniformes.
        </p>
    </section>

    <!-- MENSAJE -->
    @if ($notice)
        <div role="status"
             class="flex items-center justify-between
                    gap-3 rounded-xl border
                    border-[#DED0E6] bg-[#F5EEF8]
                    p-4 text-sm text-[#331344]">

            <span>{{ $notice }}</span>

            <button
                type="button"
                wire:click="$set('notice', '')"
                aria-label="Cerrar aviso"
                class="text-xl">
                &times;
            </button>
        </div>
    @endif

    <!-- FORMULARIO -->
    <form wire:submit="save" class="space-y-6">

        <!-- INFORMACIÓN COMERCIAL -->
        <section class="rounded-2xl border
                        border-[#EAEBEE] bg-white
                        p-5 shadow-sm sm:p-7">

            <div class="mb-6">
                <h2 class="text-lg font-semibold
                           text-[#331344]">
                    Información del negocio
                </h2>

                <p class="mt-2 text-sm text-[#716779]">
                    Datos de identificación comercial.
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label for="business-name"
                           class="mb-2 block text-sm
                                  font-semibold text-[#331344]">
                        Nombre comercial *
                    </label>

                    <input
                        id="business-name"
                        type="text"
                        wire:model="businessName"
                        maxlength="120"
                        required
                        placeholder="VK Uniformes"
                        class="min-h-12 w-full rounded-xl
                               border border-[#E3DCE7]
                               bg-[#F8F7FA] px-4
                               text-sm outline-none
                               focus:border-[#331344]"
                    >

                    @error('businessName')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="business-city"
                           class="mb-2 block text-sm
                                  font-semibold text-[#331344]">
                        Ciudad
                    </label>

                    <input
                        id="business-city"
                        type="text"
                        wire:model="city"
                        maxlength="120"
                        placeholder="Ciudad del negocio"
                        class="min-h-12 w-full rounded-xl
                               border border-[#E3DCE7]
                               bg-[#F8F7FA] px-4
                               text-sm outline-none
                               focus:border-[#331344]"
                    >

                    @error('city')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="business-email"
                           class="mb-2 block text-sm
                                  font-semibold text-[#331344]">
                        Correo de contacto
                    </label>

                    <input
                        id="business-email"
                        type="email"
                        wire:model="contactEmail"
                        maxlength="180"
                        placeholder="correo@empresa.com"
                        class="min-h-12 w-full rounded-xl
                               border border-[#E3DCE7]
                               bg-[#F8F7FA] px-4
                               text-sm outline-none
                               focus:border-[#331344]"
                    >

                    @error('contactEmail')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="business-address"
                           class="mb-2 block text-sm
                                  font-semibold text-[#331344]">
                        Dirección
                    </label>

                    <textarea
                        id="business-address"
                        wire:model="address"
                        rows="3"
                        maxlength="1000"
                        placeholder="Dirección comercial del negocio..."
                        class="w-full rounded-xl border
                               border-[#E3DCE7]
                               bg-[#F8F7FA] px-4 py-3
                               text-sm outline-none
                               focus:border-[#331344]"></textarea>

                    @error('address')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </section>

        <!-- WHATSAPP -->
        <section class="rounded-2xl border
                        border-[#EAEBEE] bg-white
                        p-5 shadow-sm sm:p-7">

            <div class="mb-6">
                <h2 class="text-lg font-semibold
                           text-[#331344]">
                    Atención por WhatsApp
                </h2>

                <p class="mt-2 text-sm leading-6
                          text-[#716779]">
                    Las solicitudes de cotización
                    y consultas se dirigirán
                    a este número.
                </p>
            </div>

            <div class="max-w-xl">

                <label for="whatsapp-number"
                       class="mb-2 block text-sm
                              font-semibold text-[#331344]">
                    Número de WhatsApp comercial
                </label>

                <div class="flex overflow-hidden
                            rounded-xl border
                            border-[#E3DCE7]
                            bg-[#F8F7FA]
                            focus-within:border-[#331344]">

                    <span class="flex min-h-12
                                 items-center border-r
                                 border-[#E3DCE7]
                                 px-4 text-sm font-bold
                                 text-[#563369]">
                        WA
                    </span>

                    <input
                        id="whatsapp-number"
                        type="tel"
                        inputmode="numeric"
                        autocomplete="tel"
                        wire:model="whatsappNumber"
                        maxlength="15"
                        placeholder="593XXXXXXXXX"
                        class="min-h-12 min-w-0 flex-1
                               bg-transparent px-4
                               text-sm outline-none"
                    >
                </div>

                <p class="mt-3 text-xs leading-6
                          text-[#716779]">
                    Escribe el código de país y el
                    número celular, sin +, espacios
                    ni guiones. Para Ecuador utiliza
                    el prefijo 593 y omite el cero inicial.
                </p>

                @error('whatsappNumber')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                <div class="mt-5 rounded-xl
                            bg-[#F8F7FA] p-4">

                    <p class="text-sm font-semibold
                              text-[#331344]">
                        ¿Cómo funcionará?
                    </p>

                    <p class="mt-2 text-xs leading-6
                              text-[#716779]">
                        Cuando un cliente seleccione
                        un uniforme y solicite una
                        cotización, se abrirá WhatsApp
                        con los datos del producto.
                    </p>

                    <p class="mt-2 text-xs leading-6
                              text-[#716779]">
                        Si dejas el número vacío,
                        los enlaces de WhatsApp
                        permanecerán deshabilitados.
                    </p>
                </div>
            </div>
        </section>

        <!-- GUARDAR -->
        <div class="flex flex-col-reverse gap-3
                    sm:flex-row sm:items-center
                    sm:justify-between">

            <p class="text-xs text-[#817689]">
                Los cambios se guardarán en MySQL.
            </p>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="save"
                class="min-h-12 rounded-xl
                       bg-[#331344] px-8
                       text-sm font-semibold text-white
                       transition hover:bg-[#563369]
                       disabled:opacity-60">

                <span wire:loading.remove
                      wire:target="save">
                    Guardar configuración
                </span>

                <span wire:loading
                      wire:target="save">
                    Guardando...
                </span>
            </button>
        </div>
    </form>
</div>
