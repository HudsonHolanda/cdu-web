<x-app-layout>
    @php
        $complaints = [
            [
                'id' => '#042',
                'title' => 'Buraco na via',
                'address' => 'Rua das Flores, 120',
                'district' => 'Centro',
                'status' => 'Pendente',
                'priority' => 'Urgente',
                'lat' => -23.5489,
                'lng' => -46.6388,
                'color' => '#ef4444',
                'description' => 'Grande crateramento na pista, com risco de acidente para veículos e pedestres.',
                'date' => '23/08/2026',
            ],
            [
                'id' => '#041',
                'title' => 'Poste sem iluminação',
                'address' => 'Avenida Brasil, 450',
                'district' => 'Vila Nova',
                'status' => 'Em Análise',
                'priority' => 'Média',
                'lat' => -23.5621,
                'lng' => -46.6462,
                'color' => '#f59e0b',
                'description' => 'Poste apagado há mais de 3 dias, causando insegurança na rua em horários noturnos.',
                'date' => '22/08/2026',
            ],
            [
                'id' => '#040',
                'title' => 'Calçada danificada',
                'address' => 'Rua da Paz, 82',
                'district' => 'Santa Helena',
                'status' => 'Resolvida',
                'priority' => 'Baixa',
                'lat' => -23.5352,
                'lng' => -46.6509,
                'color' => '#22c55e',
                'description' => 'Trecho da calçada comprometido por infiltrações e desnível, já foi regularizado.',
                'date' => '21/08/2026',
            ],
            [
                'id' => '#039',
                'title' => 'Acúmulo de lixo',
                'address' => 'Praça da Cidade, s/n',
                'district' => 'Jardim das Flores',
                'status' => 'Pendente',
                'priority' => 'Urgente',
                'lat' => -23.5404,
                'lng' => -46.6297,
                'color' => '#ef4444',
                'description' => 'Resíduos acumulados em área de convivência, com cheiro forte e presença de animais.',
                'date' => '19/08/2026',
            ],
            [
                'id' => '#038',
                'title' => 'Água parada em terreno',
                'address' => 'Rua dos Ipês, 35',
                'district' => 'Parque Central',
                'status' => 'Em Análise',
                'priority' => 'Alta',
                'lat' => -23.5466,
                'lng' => -46.6217,
                'color' => '#3b82f6',
                'description' => 'Água acumulada em terreno vago, com risco de reprodução de mosquitos.',
                'date' => '18/08/2026',
            ],
        ];

        $complaints = $mapComplaints ?? $complaints;
    @endphp

    <div class="cdu-agent-map min-h-screen bg-[#efefee] text-slate-800">
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
                        <a href="{{ route('agent.overview') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">◫</span>
                            Overview
                        </a>
                        <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">▣</span>
                            Dashboard
                        </a>
                        <a href="{{ route('agent.mapa') }}" class="flex items-center gap-3 rounded-xl bg-white/10 px-3 py-3 text-sm font-medium text-white ring-1 ring-white/10">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/10">▤</span>
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
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold tracking-tight text-slate-900">Mapa de Ocorrências</h1>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <label class="relative block w-full max-w-sm">
                            <span class="sr-only">Buscar</span>
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="search" placeholder="Buscar..." class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-11 text-sm text-slate-700 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200" disabled>
                        </label>

                        <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white shadow-sm">
                            <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80" alt="Perfil do usuário">
                        </div>
                    </div>
                </header>

                <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:p-5">
                    <div class="grid gap-5 xl:grid-cols-[2.1fr_1fr]">
                        <div class="overflow-hidden rounded-2xl border border-slate-200">
                            <div id="agent-map" class="h-[620px] w-full min-h-[620px] bg-slate-100"></div>
                        </div>

                        <aside id="complaint-preview" class="rounded-2xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Prévia do relato</p>
                            <div class="mt-4 rounded-xl border border-dashed border-slate-300 bg-white p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <h2 id="preview-title" class="text-xl font-bold text-slate-900">Selecione um ponto</h2>
                                    <span id="preview-priority" class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">-</span>
                                </div>

                                <div class="mt-4 space-y-3 text-sm text-slate-600">
                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Status</p>
                                        <p id="preview-status" class="mt-1 font-medium text-slate-800">-</p>
                                    </div>

                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Local</p>
                                        <p id="preview-address" class="mt-1 font-medium text-slate-800">-</p>
                                    </div>

                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Bairro</p>
                                        <p id="preview-district" class="mt-1 font-medium text-slate-800">-</p>
                                    </div>

                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">Descrição</p>
                                        <p id="preview-description" class="mt-1 leading-6 text-slate-700">-</p>
                                    </div>
                                </div>

                                <div class="mt-5 flex items-center justify-between border-t border-slate-200 pt-4 text-xs text-slate-500">
                                    <span id="preview-id">ID -</span>
                                    <span id="preview-date">-</span>
                                </div>
                                <a id="preview-link" href="#" class="mt-4 inline-flex text-sm font-semibold text-emerald-700 underline decoration-emerald-300 underline-offset-4 hover:text-emerald-900" hidden>
                                    Ver denúncia completa
                                </a>
                            </div>
                        </aside>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-700">
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-red-500"></span>Urgente</span>
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-amber-500"></span>Pendente</span>
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-sky-500"></span>Em Análise</span>
                        <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-emerald-500"></span>Resolvida</span>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script>
        const complaintData = @json($complaints);

        const preview = {
            title: document.getElementById('preview-title'),
            priority: document.getElementById('preview-priority'),
            status: document.getElementById('preview-status'),
            address: document.getElementById('preview-address'),
            district: document.getElementById('preview-district'),
            description: document.getElementById('preview-description'),
            id: document.getElementById('preview-id'),
            date: document.getElementById('preview-date'),
            link: document.getElementById('preview-link'),
        };

        function setPreview(complaint) {
            preview.title.textContent = complaint.title;
            preview.priority.textContent = complaint.priority;
            preview.priority.className = 'rounded-full px-2.5 py-1 text-[11px] font-semibold ' + (
                complaint.priority === 'Urgente'
                    ? 'bg-red-100 text-red-700'
                    : complaint.priority === 'Média'
                        ? 'bg-amber-100 text-amber-700'
                        : 'bg-emerald-100 text-emerald-700'
            );
            preview.status.textContent = complaint.status;
            preview.address.textContent = complaint.address;
            preview.district.textContent = complaint.district;
            preview.description.textContent = complaint.description;
            preview.id.textContent = 'ID ' + complaint.id;
            preview.date.textContent = complaint.date;
            if (complaint.publicId) {
                preview.link.href = '/denuncias/detalhe/' + encodeURIComponent(complaint.publicId);
                preview.link.hidden = false;
            } else {
                preview.link.hidden = true;
            }
        }

        const initializeAgentMap = () => {
            const mapContainer = document.getElementById('agent-map');

            if (!mapContainer) {
                return;
            }

            if (mapContainer.dataset.mapReady === 'true') {
                return;
            }

            if (!window.L) {
                window.setTimeout(initializeAgentMap, 150);
                return;
            }

            const map = window.L.map('agent-map').setView([-23.5489, -46.6388], 12);
            mapContainer.dataset.mapReady = 'true';

            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            complaintData.forEach((complaint) => {
                const marker = window.L.circleMarker([complaint.lat, complaint.lng], {
                    radius: 12,
                    color: complaint.color,
                    fillColor: complaint.color,
                    fillOpacity: 0.9,
                    weight: 2,
                }).addTo(map);

                marker.bindPopup(`
                    <div style="min-width: 180px; font-family: sans-serif;">
                        <strong>${complaint.title}</strong><br>
                        <span>${complaint.status}</span><br>
                        <span>${complaint.address}</span>
                    </div>
                `);

                marker.on('mouseover', function () {
                    setPreview(complaint);
                });

                marker.on('click', function () {
                    setPreview(complaint);
                    marker.openPopup();
                });
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeAgentMap, { once: true });
        } else {
            initializeAgentMap();
        }
        document.addEventListener('livewire:navigated', initializeAgentMap);
    </script>
</x-app-layout>
