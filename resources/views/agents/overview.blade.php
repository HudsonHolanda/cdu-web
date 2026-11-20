<x-app-layout>
@php
    $user = auth()->user();
    $roleLabel = $user?->isAdmin() ? 'Admin' : ($user?->isAgent() ? 'Agente' : 'Usuário');

    $metrics = [
        ['label' => 'Pendentes', 'value' => '48', 'tone' => 'bg-white text-slate-900', 'icon' => 'clipboard', 'accent' => 'text-amber-500'],
        ['label' => 'Em Análise', 'value' => '21', 'tone' => 'bg-white text-slate-900', 'icon' => 'folder', 'accent' => 'text-sky-500'],
        ['label' => 'Resolvidas', 'value' => '136', 'tone' => 'bg-white text-slate-900', 'icon' => 'check', 'accent' => 'text-emerald-500'],
        ['label' => 'Urgentes', 'value' => '7', 'tone' => 'bg-white text-slate-900', 'icon' => 'alert', 'accent' => 'text-rose-500'],
    ];

    $overviewComplaints = collect($complaints ?? [])->map(function ($complaint) {
        $status = $complaint['status'] ?? 'Pendente';
        $priority = $complaint['priority'] ?? 'Normal';

        $statusFilter = match ($status) {
            'Em análise' => 'analysis',
            'Em andamento' => 'in_progress',
            'Concluído' => 'resolved',
            default => 'pending',
        };

        return [
            'id' => $complaint['id'],
            'occurrence' => $complaint['title'],
            'location' => $complaint['location']['district'] ?? 'Não informado',
            'date' => $complaint['date'],
            'priority' => $priority,
            'status' => $status,
            'filter' => $statusFilter,
            'filters' => array_values(array_filter([$statusFilter, $priority === 'Urgente' ? 'urgent' : 'normal'])),
        ];
    })->values();

    $complaints = [
        ['id' => '#042', 'occurrence' => 'Buraco na via', 'location' => 'Centro', 'date' => '23/08/26', 'priority' => 'Alta', 'status' => 'Pendente', 'statusClass' => 'bg-amber-100 text-amber-700'],
        ['id' => '#041', 'occurrence' => 'Poste queimado', 'location' => 'Jd. Itália', 'date' => '23/08/26', 'priority' => 'Média', 'status' => 'Em Análise', 'statusClass' => 'bg-sky-100 text-sky-700'],
        ['id' => '#040', 'occurrence' => 'Calçada danificada', 'location' => 'Vila Drica', 'date' => '22/08/26', 'priority' => 'Baixa', 'status' => 'Resolvida', 'statusClass' => 'bg-emerald-100 text-emerald-700'],
        ['id' => '#039', 'occurrence' => 'Acúmulo de lixo', 'location' => 'Parque St. Tereza', 'date' => '21/08/26', 'priority' => 'Média', 'status' => 'Pendente', 'statusClass' => 'bg-amber-100 text-amber-700'],
        ['id' => '#038', 'occurrence' => 'Buraco na via', 'location' => 'Cohab V', 'date' => '20/08/26', 'priority' => 'Alta', 'status' => 'Em Análise', 'statusClass' => 'bg-sky-100 text-sky-700'],
    ];

    // Os valores acima preservam o protótipo visual; em produção, a tela usa o painel alimentado pelo banco.
    $metrics = $panelMetrics ?? $metrics;
    $complaints = collect($panelComplaints ?? [])->map(fn ($complaint) => [
        'id' => $complaint['id'],
        'occurrence' => $complaint['title'],
        'location' => $complaint['location']['district'],
        'date' => $complaint['date'],
        'priority' => $complaint['priority'],
        'status' => $complaint['status'],
        'statusClass' => match ($complaint['status']) {
            'Concluído' => 'bg-emerald-100 text-emerald-700',
            'Em análise' => 'bg-sky-100 text-sky-700',
            'Em andamento' => 'bg-blue-100 text-blue-700',
            'Rejeitado' => 'bg-rose-100 text-rose-700',
            default => 'bg-amber-100 text-amber-700',
        },
    ]);
@endphp

<div class="cdu-agent-page min-h-screen bg-[#efefee] text-slate-800">
    <div class="flex min-h-screen">
        <aside class="cdu-embedded-panel-sidebar hidden w-64 flex-col justify-between bg-[#1d3b34] px-5 py-6 text-white lg:flex">
            <div>
                <div class="mb-10 flex items-center gap-3 px-3">
                    <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl bg-white/10 shadow-sm ring-1 ring-white/20">
                        <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="CDU" class="h-9 w-9 object-contain" style="filter: brightness(0) invert(1);">
                    </div>
                    <div class="text-2xl font-black tracking-tight text-white">CDU</div>
                </div>

                <nav class="space-y-2">
                    <a href="{{ route('agent.overview') }}" class="flex items-center gap-3 rounded-xl bg-white/10 px-3 py-3 text-sm font-medium text-white ring-1 ring-white/10">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/10">◫</span>
                        Overview
                    </a>
                    <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">▣</span>
                        Dashboard
                    </a>
                    <a href="{{ route('agent.mapa') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">▤</span>
                        Mapa
                    </a>
                    <a href="{{ route('agents.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">◎</span>
                        Usuários
                    </a>
                    <a href="{{ route('agent.relatorios') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">◌</span>
                        Relatórios
                    </a>
                </nav>
            </div>

            <div class="space-y-2 border-t border-white/10 pt-4">
                <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                    <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">⚙</span>
                    Configurações
                </a>
                <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                    <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">↩</span>
                    Sair
                </a>
            </div>
        </aside>

        <main class="flex-1 p-4 md:p-6 xl:p-8">
            <header class="cdu-embedded-panel-header flex flex-col gap-4 rounded-2xl border border-slate-200 bg-[#f7f7f5] px-4 py-4 shadow-sm sm:px-5 lg:flex-row lg:items-center lg:justify-between">
                <label class="relative block w-full max-w-xl">
                    <span class="sr-only">Buscar</span>
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="search" placeholder="Buscar..." class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-11 text-sm text-slate-700 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200" disabled>
                </label>

                <div class="flex items-center justify-end gap-3">
                    <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white shadow-sm">
                        <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80" alt="Perfil do usuário">
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-slate-800">{{ $user?->name ?? 'Pedro Alves Cabral' }}</div>
                        <div class="text-xs text-slate-500">{{ $roleLabel }}</div>
                    </div>
                </div>
            </header>

            <section class="mt-8">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Bom dia, {{ $roleLabel }}!</h1>
                <p class="mt-2 text-base text-slate-600">Aqui está o panorama geral das ocorrências da cidade.</p>
            </section>

            <section class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($metrics as $metric)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 {{ $metric['accent'] }}">
                                @switch($metric['icon'])
                                    @case('clipboard')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        @break
                                    @case('folder')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2V7z" />
                                        </svg>
                                        @break
                                    @case('check')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 3" />
                                        </svg>
                                        @break
                                    @case('clock')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <circle cx="12" cy="12" r="8.5" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5v5l3 1.75" />
                                        </svg>
                                        @break
                                    @case('minus')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                        </svg>
                                        @break
                                    @case('alert')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                        </svg>
                                        @break
                                @endswitch
                            </div>
                            <button type="button" class="overview-filter-trigger flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition hover:bg-slate-200" data-overview-filter="{{ $metric['filter'] ?? '' }}" aria-label="Filtrar denúncias: {{ $metric['label'] }}" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                        <div class="mt-6">
                            <div class="text-4xl font-black tracking-tight text-slate-900">{{ $metric['value'] }}</div>
                            <div class="mt-1 text-lg font-medium text-slate-700">{{ $metric['label'] }}</div>
                        </div>
                    </article>
                @endforeach
            </section>

            <script type="application/json" id="overview-complaints-data">@json($overviewComplaints)</script>

            <section class="mt-8 grid gap-6 xl:grid-cols-[1.8fr_1fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5" data-overview-results-panel>
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-xl font-semibold text-slate-900" data-overview-filter-title>Ocorrências Recentes</h2>
                        <div class="flex gap-2">
                            <button type="button" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700" data-overview-filter="all">Ver Todas</button>
                            <button type="button" class="rounded-lg border border-slate-200 bg-slate-900 px-3 py-2 text-sm font-medium text-white" data-overview-map-open>Abrir Mapa</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-left text-sm text-slate-700">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-500">
                                    <th class="px-3 py-3 font-medium">ID</th>
                                    <th class="px-3 py-3 font-medium">Ocorrência</th>
                                    <th class="px-3 py-3 font-medium">Localização</th>
                                    <th class="px-3 py-3 font-medium">Data</th>
                                    <th class="px-3 py-3 font-medium">Prioridade</th>
                                    <th class="px-3 py-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody data-overview-filter-rows>
                                @foreach ($complaints as $complaint)
                                    <tr class="border-b border-slate-100 last:border-b-0">
                                        <td class="px-3 py-3 font-medium text-slate-500">{{ $complaint['id'] }}</td>
                                        <td class="px-3 py-3">{{ $complaint['occurrence'] }}</td>
                                        <td class="px-3 py-3">{{ $complaint['location'] }}</td>
                                        <td class="px-3 py-3">{{ $complaint['date'] }}</td>
                                        <td class="px-3 py-3">
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                                {{ $complaint['priority'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $complaint['statusClass'] }}">
                                                {{ $complaint['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="hidden py-8 text-center text-sm text-slate-500" data-overview-filter-empty>Nenhuma denúncia encontrada para este filtro.</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-slate-900">Mapa de Ocorrências</h2>
                    </div>
                    <div id="complaints-map" class="h-[360px] w-full overflow-hidden rounded-xl border border-slate-200"></div>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-600">
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-red-500"></span>Urgente</span>
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-amber-400"></span>Pendente</span>
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-sky-500"></span>Em Análise</span>
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-emerald-500"></span>Resolvida</span>
                    </div>
                </div>
            </section>
        </main>
    </div>
</div>

<div id="overview-map-modal" class="fixed inset-0 z-[60] hidden bg-slate-950/60 p-3 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="overview-map-modal-title">
    <div class="flex h-full w-full items-center justify-center" data-overview-map-backdrop>
        <div class="w-full max-w-6xl overflow-hidden rounded-2xl bg-white shadow-2xl cdu-agent-modal-surface">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3 sm:px-6">
                <div>
                    <h2 id="overview-map-modal-title" class="text-lg font-semibold text-slate-900">Mapa de Ocorrências</h2>
                    <p class="text-sm text-slate-500">Visualização ampliada das denúncias cadastradas</p>
                </div>
                <button type="button" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-xl text-slate-600 transition hover:bg-slate-200" data-overview-map-close aria-label="Fechar mapa">&times;</button>
            </div>
            <div id="overview-modal-map" class="h-[70vh] min-h-[360px] w-full"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    const initializeOverviewMaps = () => {
        const pageContainer = document.getElementById('complaints-map');
        if (!pageContainer || pageContainer.dataset.mapReady === 'true') return;
        if (!window.L) {
            window.setTimeout(initializeOverviewMaps, 150);
            return;
        }

        const markers = @json($mapMarkers ?? []);
        const renderMarkers = (map, radius = 10, filter = 'all') => {
            const layers = [];
            markers.filter((marker) => filter === 'all' || marker.filters?.includes(filter)).forEach((marker) => {
                const circle = L.circleMarker(marker.coords, {
                    radius,
                    color: marker.color,
                    fillColor: marker.color,
                    fillOpacity: 0.9,
                    weight: 2,
                }).addTo(map);

                circle.bindPopup(`<strong>${marker.label}</strong>`);
                layers.push(circle);
            });
            return layers;
        };

        const clearMarkers = (layers) => layers.forEach((layer) => layer.remove());

        const addTiles = (map) => L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const pageMap = L.map('complaints-map').setView([-23.5489, -46.6388], 12);
        pageContainer.dataset.mapReady = 'true';
        addTiles(pageMap);
        let pageMarkers = [];
        let modalMarkers = [];
        let activeFilter = 'all';
        let modalMap;
        const updateMapMarkers = (filter) => {
            activeFilter = filter;
            clearMarkers(pageMarkers);
            pageMarkers = renderMarkers(pageMap, 10, activeFilter) || [];
            if (modalMap) {
                clearMarkers(modalMarkers);
                modalMarkers = renderMarkers(modalMap, 12, activeFilter) || [];
            }
        };
        updateMapMarkers(activeFilter);

        const modal = document.getElementById('overview-map-modal');
        const modalMapContainer = document.getElementById('overview-modal-map');
        const openButton = document.querySelector('[data-overview-map-open]');
        const closeButton = document.querySelector('[data-overview-map-close]');
        const backdrop = document.querySelector('[data-overview-map-backdrop]');
        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        const openModal = () => {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            if (!modalMap) {
                modalMap = L.map(modalMapContainer).setView([-23.5489, -46.6388], 12);
                addTiles(modalMap);
                modalMarkers = renderMarkers(modalMap, 12, activeFilter) || [];
            }

            window.setTimeout(() => modalMap.invalidateSize(), 50);
        };

        openButton?.addEventListener('click', openModal);
        closeButton?.addEventListener('click', closeModal);
        window.addEventListener('overview-map-filter', (event) => updateMapMarkers(event.detail?.filter || 'all'));
        backdrop?.addEventListener('click', (event) => {
            if (event.target === backdrop) closeModal();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeOverviewMaps, { once: true });
    } else {
        initializeOverviewMaps();
    }
    document.addEventListener('livewire:navigated', initializeOverviewMaps);
</script>
</x-app-layout>
