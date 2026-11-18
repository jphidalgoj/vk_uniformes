<div>
    <footer class="vk-footer">
        @if ($site['announcement'] ?? '')<div class="vk-footer-announcement">{{ $site['announcement'] }}</div>@endif
        <div class="vk-footer-grid">
            <div><h2>Cerca de ti</h2><p>{{ $site['footer_text'] ?? 'Uniformes profesionales para acompañarte cada día.' }}</p>@if ($city)<p>{{ $city }}</p>@endif @if ($address)<p class="whitespace-pre-line">{{ $address }}</p>@endif</div>
            <div><h2>Nuestras colecciones</h2><nav aria-label="Colecciones del pie">@foreach ($categories as $category)<a href="{{ route('store.category', $category->slug) }}">{{ $category->name }}</a>@endforeach<a href="{{ route('store.catalog') }}">Catálogo completo</a></nav></div>
            <div><h2>Servicio al cliente</h2><nav aria-label="Atención al cliente">@if ($whatsappUrl)<a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Cotiza por WhatsApp ↗</a>@endif @if ($contactEmail)<a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a>@endif @foreach (['privacy' => 'Política de privacidad', 'terms' => 'Términos y condiciones'] as $key => $label)@if ($site[$key] ?? '')<a href="{{ $site[$key] }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>@endif @endforeach</nav></div>
            <div><h2>Conéctate con nosotros</h2><div class="vk-socials">@foreach (['instagram' => 'Instagram', 'facebook' => 'Facebook'] as $key => $label)@if ($site[$key] ?? '')<a href="{{ $site[$key] }}" target="_blank" rel="noopener noreferrer">{{ $label }} ↗</a>@endif @endforeach</div>@if ($site['hours'] ?? '')<h3>Horario de atención</h3><p class="whitespace-pre-line">{{ $site['hours'] }}</p>@endif<img src="{{ asset($site['logo'] ?? 'images/logo-vk.png') }}" alt="{{ $businessName }}" width="170" height="70" loading="lazy" class="vk-footer-logo"></div>
        </div>
        <div class="vk-footer-bottom">© {{ now()->year }} {{ $businessName }}. Todos los derechos reservados.</div>
    </footer>
    @if ($whatsappUrl)<a class="vk-whatsapp" href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Consultar con {{ $businessName }} por WhatsApp">WhatsApp ↗</a>@endif
</div>
