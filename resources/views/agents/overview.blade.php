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

    $complaints = [
        ['id' => '#042', 'occurrence' => 'Buraco na via', 'location' => 'Centro', 'date' => '23/08/26', 'priority' => 'Alta', 'status' => 'Pendente', 'statusClass' => 'bg-amber-100 text-amber-700'],
        ['id' => '#041', 'occurrence' => 'Poste queimado', 'location' => 'Jd. Itália', 'date' => '23/08/26', 'priority' => 'Média', 'status' => 'Em Análise', 'statusClass' => 'bg-sky-100 text-sky-700'],
        ['id' => '#040', 'occurrence' => 'Calçada danificada', 'location' => 'Vila Drica', 'date' => '22/08/26', 'priority' => 'Baixa', 'status' => 'Resolvida', 'statusClass' => 'bg-emerald-100 text-emerald-700'],
        ['id' => '#039', 'occurrence' => 'Acúmulo de lixo', 'location' => 'Parque St. Tereza', 'date' => '21/08/26', 'priority' => 'Média', 'status' => 'Pendente', 'statusClass' => 'bg-amber-100 text-amber-700'],
        ['id' => '#038', 'occurrence' => 'Buraco na via', 'location' => 'Cohab V', 'date' => '20/08/26', 'priority' => 'Alta', 'status' => 'Em Análise', 'statusClass' => 'bg-sky-100 text-sky-700'],
    ];
@endphp

<div class="min-h-screen bg-[#efefee] text-slate-800">
    <div class="flex min-h-screen">
        <aside class="hidden w-64 flex-col justify-between bg-[#1d3b34] px-5 py-6 text-white lg:flex">
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
            <header class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-[#f7f7f5] px-4 py-4 shadow-sm sm:px-5 lg:flex-row lg:items-center lg:justify-between">
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
                                    @case('alert')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                        </svg>
                                        @break
                                @endswitch
                            </div>
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-6">
                            <div class="text-4xl font-black tracking-tight text-slate-900">{{ $metric['value'] }}</div>
                            <div class="mt-1 text-lg font-medium text-slate-700">{{ $metric['label'] }}</div>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="mt-8 grid gap-6 xl:grid-cols-[1.8fr_1fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-xl font-semibold text-slate-900">Ocorrências Recentes</h2>
                        <div class="flex gap-2">
                            <button class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">Ver Todas</button>
                            <button class="rounded-lg border border-slate-200 bg-slate-900 px-3 py-2 text-sm font-medium text-white">Abrir Mapa</button>
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
                            <tbody>
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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('complaints-map').setView([-23.5489, -46.6388], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = [
            { coords: [-23.549, -46.638], color: '#ef4444', label: 'Urgente' },
            { coords: [-23.562, -46.646], color: '#f59e0b', label: 'Pendente' },
            { coords: [-23.540, -46.630], color: '#0ea5e9', label: 'Em Análise' },
            { coords: [-23.535, -46.650], color: '#10b981', label: 'Resolvida' },
            { coords: [-23.555, -46.620], color: '#f59e0b', label: 'Pendente' },
        ];

        markers.forEach((marker) => {
            const circle = L.circleMarker(marker.coords, {
                radius: 10,
                color: marker.color,
                fillColor: marker.color,
                fillOpacity: 0.9,
                weight: 2,
            }).addTo(map);

            circle.bindPopup(`<strong>${marker.label}</strong>`);
        });
    });
</script>
</x-app-layout>
