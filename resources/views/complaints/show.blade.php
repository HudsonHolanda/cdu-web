<x-app-layout>
    @php
        $location = $complaint['location'];
        $attachments = $complaint['attachments'] ?? [$complaint['imageUrl']];
        $mapUrl = 'https://www.openstreetmap.org/?mlat='.$location['latitude'].'&mlon='.$location['longitude'].'#map=17/'.$location['latitude'].'/'.$location['longitude'];
    @endphp
    <div class="complaints-page complaint-detail-page">
        <a class="complaint-back-link" href="{{ route('complaints.index') }}"><span aria-hidden="true">←</span> Voltar para denúncias</a>

        @if (session('success'))
            <div class="complaint-success-message" role="status">{{ session('success') }}</div>
        @endif

        <div class="complaint-detail-heading">
            <div>
                <p class="complaints-eyebrow">{{ $complaint['category'] ?? 'Relato da comunidade' }} · {{ $location['district'] }}</p>
                <h1>{{ $complaint['title'] }}</h1>
                <div class="complaint-detail-meta">
                    <span>{{ \Illuminate\Support\Carbon::parse($complaint['createdAt'])->format('d/m/Y · H:i') }}</span>
                    <span class="complaint-detail-dot" aria-hidden="true">·</span>
                    <span>{{ $location['address'] }}</span>
                </div>
            </div>
            <span class="complaint-status complaint-status--{{ \Illuminate\Support\Str::slug($complaint['status']) }}">{{ $complaint['status'] }}</span>
        </div>

        <nav class="complaint-detail-tabs" aria-label="Seções da denúncia">
            <a href="#detalhes">Detalhes</a>
            <a href="#anexos">Anexos ({{ count($attachments) }})</a>
            <a href="#localizacao">Localização</a>
            <a href="#andamento">Andamento</a>
        </nav>

        <div class="complaint-detail-layout">
            <main class="complaint-detail-main">
                <section id="anexos" class="complaint-detail-panel complaint-attachments" aria-labelledby="attachments-title">
                    <div class="complaint-section-heading">
                        <div><p class="complaint-section-kicker">Registro visual</p><h2 id="attachments-title">Fotos do local</h2></div>
                        <span>{{ count($attachments) }} {{ count($attachments) === 1 ? 'imagem' : 'imagens' }}</span>
                    </div>
                    <div class="complaint-attachment-grid">
                        @foreach ($attachments as $index => $filename)
                            <a class="complaint-attachment {{ $loop->first ? 'complaint-attachment--primary' : '' }}" href="{{ route('complaints.image', ['filename' => basename($filename)]) }}" target="_blank" rel="noopener">
                                <img src="{{ route('complaints.image', ['filename' => basename($filename)]) }}" alt="{{ $index === 0 ? 'Foto principal' : 'Foto complementar '.$loop->iteration }} da denúncia: {{ $complaint['title'] }}" loading="lazy">
                            </a>
                        @endforeach
                    </div>
                </section>

                <section id="detalhes" class="complaint-detail-panel complaint-description-panel" aria-labelledby="description-title">
                    <p class="complaint-section-kicker">Sobre o relato</p>
                    <h2 id="description-title">Descrição</h2>
                    <p>{{ $complaint['description'] }}</p>
                </section>

                <section id="localizacao" class="complaint-detail-panel complaint-location-panel" aria-labelledby="location-title">
                    <div class="complaint-section-heading">
                        <div><p class="complaint-section-kicker">Onde acontece</p><h2 id="location-title">Localização</h2></div>
                    </div>
                    <address>
                        <strong>{{ $location['address'] }}</strong><br>
                        {{ $location['district'] }} · CEP {{ $location['cep'] }}
                        @if (!empty($location['complement']))<br>{{ $location['complement'] }}@endif
                    </address>
                    <div class="complaint-map-card" role="img" aria-label="Coordenadas aproximadas da denúncia: {{ $location['latitude'] }}, {{ $location['longitude'] }}">
                        <span class="complaint-map-pin" aria-hidden="true">⌖</span>
                        <div><strong>{{ $location['district'] }}</strong><span>{{ $location['latitude'] }}, {{ $location['longitude'] }}</span></div>
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener">Abrir mapa <span aria-hidden="true">↗</span></a>
                    </div>
                </section>
            </main>

            <aside class="complaint-detail-sidebar" aria-label="Resumo da denúncia">
                <section class="complaint-detail-panel complaint-summary-card">
                    <h2>Informações</h2>
                    <dl>
                        <div><dt>Status</dt><dd><span class="complaint-status complaint-status--{{ \Illuminate\Support\Str::slug($complaint['status']) }}">{{ $complaint['status'] }}</span></dd></div>
                        <div><dt>Prioridade</dt><dd><span class="complaint-priority {{ $complaint['highPriority'] ? 'complaint-priority--high' : '' }}">{{ $complaint['highPriority'] ? 'Alta' : 'Normal' }}</span></dd></div>
                        <div><dt>Registrada em</dt><dd>{{ \Illuminate\Support\Carbon::parse($complaint['createdAt'])->format('d/m/Y') }}</dd></div>
                        <div><dt>Região</dt><dd>{{ $location['district'] }}</dd></div>
                    </dl>
                </section>

                <section id="andamento" class="complaint-detail-panel complaint-history-card">
                    <p class="complaint-section-kicker">Acompanhamento</p>
                    <h2>Andamento</h2>
                    <div class="complaint-timeline">
                        <span class="complaint-timeline-marker" aria-hidden="true"></span>
                        <div><strong>Denúncia registrada</strong><span>{{ \Illuminate\Support\Carbon::parse($complaint['createdAt'])->format('d/m/Y · H:i') }}</span></div>
                    </div>
                    <p class="complaint-history-note">Novas atualizações aparecerão aqui conforme o atendimento avançar.</p>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
