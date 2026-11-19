<x-app-layout>
    @php
        $user = auth()->user();
        $roleLabel = $user?->isAdmin() ? 'Admin' : ($user?->isAgent() ? 'Agente' : 'Usuário');

        $metrics = [
            ['label' => 'Total de denúncias', 'value' => '1.248', 'delta' => '+12%', 'tone' => 'bg-amber-100 text-amber-700', 'icon' => 'clipboard'],
            ['label' => 'Pendentes', 'value' => '312', 'delta' => '+8%', 'tone' => 'bg-orange-100 text-orange-700', 'icon' => 'clock'],
            ['label' => 'Em análise', 'value' => '478', 'delta' => '+5%', 'tone' => 'bg-sky-100 text-sky-700', 'icon' => 'search'],
            ['label' => 'Resolvidas', 'value' => '458', 'delta' => '+18%', 'tone' => 'bg-emerald-100 text-emerald-700', 'icon' => 'check'],
        ];

        $recentComplaints = [
            ['id' => '#042', 'occurrence' => 'Buraco na via', 'location' => 'Centro', 'date' => '23/08/26', 'priority' => 'Alta', 'status' => 'Pendente'],
            ['id' => '#041', 'occurrence' => 'Poste queimado', 'location' => 'Jd. Itália', 'date' => '23/08/26', 'priority' => 'Média', 'status' => 'Em análise'],
            ['id' => '#040', 'occurrence' => 'Calçada danificada', 'location' => 'Vila Drica', 'date' => '22/08/26', 'priority' => 'Baixa', 'status' => 'Resolvida'],
            ['id' => '#039', 'occurrence' => 'Acúmulo de lixo', 'location' => 'Parque St. Tereza', 'date' => '21/08/26', 'priority' => 'Média', 'status' => 'Pendente'],
            ['id' => '#038', 'occurrence' => 'Buraco na via', 'location' => 'Cohab V', 'date' => '20/08/26', 'priority' => 'Alta', 'status' => 'Em análise'],
        ];

        $metrics = collect($panelMetrics ?? $metrics)->map(fn ($metric) => [
            ...$metric,
            'value' => number_format((int) $metric['value'], 0, ',', '.'),
            'delta' => '',
            'tone' => 'bg-slate-100 text-slate-700',
        ])->all();
        $recentComplaints = collect($panelComplaints ?? $recentComplaints)->map(fn ($complaint) => [
            'id' => $complaint['id'],
            'occurrence' => $complaint['title'],
            'location' => $complaint['location']['district'],
            'date' => $complaint['date'],
            'priority' => $complaint['priority'],
            'status' => $complaint['status'],
        ])->all();
    @endphp

    <div class="min-h-screen bg-[#efefee] text-slate-800">
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
                        <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-3 rounded-xl bg-white/10 px-3 py-3 text-sm font-medium text-white ring-1 ring-white/10">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/10">▣</span>
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
                        <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
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
                    <div class="relative block w-full max-w-xl">
                        <span class="sr-only">Buscar</span>
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="search" placeholder="Search" class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-11 text-sm text-slate-700 outline-none transition focus:border-slate-300 focus:ring-2 focus:ring-slate-200" disabled>
                    </div>

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
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Dashboard</h1>
                            <p class="mt-2 text-base text-slate-600">Visão geral das ocorrências registradas na cidade.</p>
                        </div>

                        <div class="flex gap-3">
                            <div class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                                <span>📅</span>
                                01/08/2026 - 24/08/2026
                            </div>
                            <button class="rounded-xl border border-slate-200 bg-slate-900 px-4 py-2 text-sm font-medium text-white">Exportar relatório</button>
                        </div>
                    </div>
                </section>

                <section class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($metrics as $metric)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $metric['tone'] }}">
                                    @switch($metric['icon'])
                                        @case('clipboard')
                                            <span>▣</span>
                                            @break
                                        @case('clock')
                                            <span>◔</span>
                                            @break
                                        @case('search')
                                            <span>⌕</span>
                                            @break
                                        @case('check')
                                            <span>✓</span>
                                            @break
                                    @endswitch
                                </div>
                                <span class="text-xs font-medium text-emerald-600">{{ $metric['delta'] }}</span>
                            </div>
                            <div class="mt-5 text-3xl font-black text-slate-900">{{ $metric['value'] }}</div>
                            <div class="mt-1 text-sm text-slate-600">{{ $metric['label'] }}</div>
                        </article>
                    @endforeach
                </section>

                <section class="mt-8 grid gap-6 xl:grid-cols-2">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-slate-900">Denúncias por categoria</h2>
                        </div>

                        <div class="flex flex-col gap-5 lg:flex-row lg:items-center">
                            <div class="relative mx-auto h-52 w-52 rounded-full bg-[conic-gradient(#0f766e_0_34%,#f59e0b_34%_55%,#d946ef_55%_73%,#3b82f6_73%_88%,#94a3b8_88%_100%)] p-5">
                                <div class="flex h-full w-full items-center justify-center rounded-full bg-white text-center shadow-inner">
                                    <div>
                                        <div class="text-4xl font-black text-slate-900">{{ number_format($totalComplaints ?? 0, 0, ',', '.') }}</div>
                                        <div class="text-sm text-slate-500">Total</div>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full space-y-3 text-sm text-slate-700">
                                @foreach (($categoryCounts ?? collect())->take(5) as $category => $count)
                                    <div class="flex items-center justify-between gap-3"><span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-[#0f766e]"></span>{{ $category ?: 'Sem categoria' }}</span><span>{{ $count }} ({{ $totalComplaints ? round($count / $totalComplaints * 100) : 0 }}%)</span></div>
                                @endforeach
                            </div>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-slate-900">Evolução das denúncias</h2>
                            <select class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                <option>Por dia</option>
                            </select>
                        </div>

                        <svg viewBox="0 0 600 220" class="h-52 w-full" preserveAspectRatio="none">
                            <path d="M0,170 L60,150 L120,160 L180,120 L240,130 L300,90 L360,100 L420,40 L480,75 L540,50 L600,60" fill="none" stroke="#1f2937" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            <g fill="#1f2937" font-size="10" font-family="sans-serif">
                                <text x="0" y="210">01/08</text>
                                <text x="100" y="210">06/08</text>
                                <text x="200" y="210">11/08</text>
                                <text x="300" y="210">16/08</text>
                                <text x="400" y="210">21/08</text>
                                <text x="500" y="210">24/08</text>
                            </g>
                        </svg>
                    </article>
                </section>

                <section class="mt-8 grid gap-6 xl:grid-cols-3">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="mb-5 text-xl font-semibold text-slate-900">Denúncias por status</h2>
                        <div class="space-y-4 text-sm">
                            @foreach (($statusCounts ?? collect())->take(5) as $status => $count)
                                <div>
                                    <div class="mb-1 flex justify-between"><span>{{ $status }}</span><span class="font-semibold">{{ $count }} ({{ $totalComplaints ? round($count / $totalComplaints * 100) : 0 }}%)</span></div>
                                    <div class="h-2.5 rounded-full bg-slate-100"><div class="h-full rounded-full bg-emerald-500" style="width: {{ $totalComplaints ? round($count / $totalComplaints * 100) : 0 }}%"></div></div>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="mb-5 text-xl font-semibold text-slate-900">Denúncias por região</h2>
                        <div class="flex items-center justify-center pt-5">
                            <div class="relative h-32 w-44">
                                <div class="absolute left-3 top-7 h-24 w-24 rounded-full bg-emerald-200/70 blur-sm"></div>
                                <div class="absolute left-10 top-0 h-16 w-16 rounded-full bg-emerald-300/80"></div>
                                <div class="absolute right-2 top-8 h-20 w-20 rounded-full bg-emerald-100/80"></div>
                            </div>
                        </div>
                        <div class="mt-5 space-y-3 text-sm text-slate-700">
                            @foreach (($districtCounts ?? collect())->take(4) as $district => $count)
                                <div class="flex items-center justify-between"><span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-emerald-400"></span>{{ $district }}</span><span>{{ $count }}</span></div>
                            @endforeach
                        </div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="mb-5 text-xl font-semibold text-slate-900">Top 5 bairros</h2>
                        <div class="space-y-4 text-sm">
                            @foreach (($districtCounts ?? collect())->take(5) as $district => $count)
                                <div class="flex items-center justify-between"><span>{{ $loop->iteration }}. {{ $district }}</span><span>{{ $count }}</span></div>
                            @endforeach
                        </div>
                    </article>
                </section>

                <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-slate-900">Ocorrências recentes</h2>
                        <button class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">Ver todas</button>
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
                                    <th class="px-3 py-3 font-medium">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentComplaints as $complaint)
                                    <tr class="border-b border-slate-100 last:border-b-0">
                                        <td class="px-3 py-3 font-medium text-slate-500">{{ $complaint['id'] }}</td>
                                        <td class="px-3 py-3">{{ $complaint['occurrence'] }}</td>
                                        <td class="px-3 py-3">{{ $complaint['location'] }}</td>
                                        <td class="px-3 py-3">{{ $complaint['date'] }}</td>
                                        <td class="px-3 py-3">
                                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">
                                                {{ $complaint['priority'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-700">
                                                {{ $complaint['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <button class="text-sm font-medium text-slate-700 hover:text-slate-900">Ver</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</x-app-layout>
