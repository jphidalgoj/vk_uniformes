{{-- Cookies Policy Page --}}
@php
    $title = $businessName . ' | Política de cookies';
@endphp

<div class="storefront-legal mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:py-16">
    {{-- Breadcrumb --}}
    <nav class="mb-8 flex items-center gap-2 text-xs text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-[#331344]">Inicio</a>
        <span>/</span>
        <span class="font-medium text-gray-800">Política de cookies</span>
    </nav>

    <h1 class="mb-2 text-3xl font-bold text-gray-900">Política de cookies</h1>
    <p class="mb-10 text-sm text-gray-500">Última actualización: {{ now()->format('d \d\e F \d\e Y') }}</p>

    <div class="prose prose-sm max-w-none prose-headings:font-semibold prose-headings:text-gray-900 prose-p:text-gray-600 prose-li:text-gray-600">
        <h2>¿Qué son las cookies?</h2>
        <p>Una cookie es un pequeño archivo de texto que los sitios web pueden almacenar en el dispositivo del usuario para registrar información sobre la navegación.</p>

        <h2>Tipos de cookies que utilizamos</h2>
        <ul class="list-disc list-inside">
            <li><strong>Cookies técnicas:</strong> necesarias para el funcionamiento básico del sitio.</li>
            <li><strong>Cookies de análisis:</strong> nos ayudan a entender el comportamiento de los usuarios para mejorar el servicio.</li>
            <li><strong>Cookies de personalización:</strong> permiten adaptar el contenido a sus preferencias.</li>
        </ul>

        <h2>Gestión de cookies</h2>
        <p>Usted puede configurar su navegador para bloquear o eliminar cookies. Consulte la sección de ayuda de su navegador para obtener instrucciones.</p>
    </div>

    <p class="mt-8 text-sm text-gray-600">Para cualquier consulta sobre el uso de cookies, contáctenos a través de nuestro WhatsApp.</p>
</div>
