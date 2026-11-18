
<div class="storefront-legal mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:py-16">

    {{-- Breadcrumb --}}
    <nav class="mb-8 flex items-center gap-2 text-xs text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-[#331344]">Inicio</a>
        <span>/</span>
        <span class="font-medium text-gray-800">Política de privacidad</span>
    </nav>

    <h1 class="mb-2 text-3xl font-bold text-gray-900">Política de privacidad</h1>
    <p class="mb-10 text-sm text-gray-500">Última actualización: {{ now()->format('d \d\e F \d\e Y') }}</p>

    <div class="prose prose-sm max-w-none prose-headings:font-semibold prose-headings:text-gray-900 prose-p:text-gray-600 prose-li:text-gray-600">

        <h2>1. Responsable del tratamiento</h2>
        <p>
            <strong>{{ $businessName }}</strong> (en adelante, "nosotros" o "la empresa") es responsable del
            tratamiento de los datos personales que usted nos proporciona a través de nuestro sitio web y
            cualquier otro canal de comunicación. Nos comprometemos a proteger su privacidad y a tratar
            sus datos conforme a la legislación vigente.
        </p>

        <h2>2. Datos que recopilamos</h2>
        <p>Podemos recopilar los siguientes tipos de información:</p>
        <ul>
            <li><strong>Datos de contacto:</strong> nombre, correo electrónico, número de teléfono o WhatsApp.</li>
            <li><strong>Datos de navegación:</strong> páginas visitadas, tiempo de permanencia, dispositivo y navegador utilizado.</li>
            <li><strong>Datos de pedido:</strong> productos consultados, tallas, colores y cualquier información proporcionada al realizar una consulta o pedido.</li>
            <li><strong>Datos técnicos:</strong> dirección IP, tipo de navegador, zona horaria y datos de cookies (ver nuestra <a href="{{ route('store.cookies') }}">Política de cookies</a>).</li>
        </ul>

        <h2>3. Finalidad del tratamiento</h2>
        <p>Utilizamos sus datos para:</p>
        <ul>
            <li>Responder sus consultas y cotizaciones enviadas por WhatsApp o correo electrónico.</li>
            <li>Gestionar pedidos y personalización de uniformes.</li>
            <li>Mejorar la experiencia de navegación en nuestro sitio web.</li>
            <li>Enviarle información sobre nuestros productos y colecciones (únicamente si nos ha dado su consentimiento).</li>
            <li>Cumplir con obligaciones legales aplicables.</li>
        </ul>

        <h2>4. Base legal del tratamiento</h2>
        <p>El tratamiento de sus datos se basa en:</p>
        <ul>
            <li><strong>Consentimiento:</strong> cuando usted nos contacta o completa un formulario.</li>
            <li><strong>Interés legítimo:</strong> para mejorar nuestros servicios y la seguridad del sitio.</li>
            <li><strong>Obligación legal:</strong> cuando la ley nos exige conservar ciertos datos.</li>
        </ul>

        <h2>5. Conservación de datos</h2>
        <p>
            Conservamos sus datos personales únicamente durante el tiempo necesario para cumplir con los fines
            descritos en esta política o según lo exija la legislación aplicable. Los datos de contacto se
            conservan mientras exista una relación comercial activa o hasta que nos solicite su eliminación.
        </p>

        <h2>6. Compartición con terceros</h2>
        <p>
            No vendemos ni cedemos sus datos a terceros con fines comerciales. Podemos compartir información con:
        </p>
        <ul>
            <li>Proveedores de servicios tecnológicos (hosting, analytics) que actúan como encargados del tratamiento bajo contrato.</li>
            <li>Autoridades competentes cuando la ley lo exija.</li>
        </ul>

        <h2>7. Sus derechos</h2>
        <p>Usted tiene derecho a:</p>
        <ul>
            <li><strong>Acceder</strong> a los datos personales que conservamos sobre usted.</li>
            <li><strong>Rectificar</strong> datos inexactos o incompletos.</li>
            <li><strong>Suprimir</strong> sus datos cuando ya no sean necesarios.</li>
            <li><strong>Oponerse</strong> al tratamiento o solicitar su <strong>limitación</strong>.</li>
            <li><strong>Portabilidad:</strong> recibir sus datos en un formato estructurado.</li>
        </ul>
        <p>
            Para ejercer cualquiera de estos derechos, contáctenos a través de nuestro WhatsApp o correo electrónico
            indicando el asunto "Protección de datos".
        </p>

        <h2>8. Seguridad</h2>
        <p>
            Implementamos medidas técnicas y organizativas apropiadas para proteger sus datos personales contra
            el acceso no autorizado, la alteración, divulgación o destrucción. Sin embargo, ningún sistema de
            transmisión por internet es 100% seguro.
        </p>

        <h2>9. Cookies</h2>
        <p>
            Nuestro sitio web utiliza cookies propias y de terceros. Para más información, consulte nuestra
            <a href="{{ route('store.cookies') }}">Política de cookies</a>.
        </p>

        <h2>10. Cambios en esta política</h2>
        <p>
            Podemos actualizar esta Política de privacidad periódicamente. Le notificaremos cualquier cambio
            significativo publicando la nueva versión en esta página con la fecha de actualización revisada.
            Le recomendamos revisar esta página regularmente.
        </p>

        <h2>11. Contacto</h2>
        <p>
            Si tiene preguntas sobre esta política o sobre el tratamiento de sus datos, puede contactarnos a través
            de los canales disponibles en nuestra página de inicio.
        </p>
    </div>

    {{-- Enlace a cookies --}}
    <div class="mt-10 rounded-2xl border border-[#E3DCE7] bg-[#F8F7FA] p-6">
        <p class="text-sm text-gray-600">
            ¿Desea saber cómo usamos las cookies?
            <a href="{{ route('store.cookies') }}" class="font-semibold text-[#331344] underline-offset-2 hover:underline">
                Consulte nuestra Política de cookies →
            </a>
        </p>
    </div>
</div>
