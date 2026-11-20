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

        $dashboardComplaints = collect($complaints ?? [])->values();
        $dashboardFilters = $dashboardFilters ?? [];
        $dashboardPeriod = 'month';
        $dashboardBaseFilters = collect($dashboardFilters)->except('status')->all();

        $metrics = collect($panelMetrics ?? $metrics)->map(fn ($metric) => [
            ...$metric,
            'value' => number_format((int) $metric['value'], 0, ',', '.'),
            'delta' => '',
            'tone' => 'bg-slate-100 text-slate-700',
        ])->all();
        $recentComplaints = collect($panelComplaints ?? $recentComplaints)->map(fn ($complaint) => [
            'id' => $complaint['id'],
            'publicId' => $complaint['publicId'],
            'occurrence' => $complaint['title'],
            'location' => $complaint['location']['district'],
            'date' => $complaint['date'],
            'priority' => $complaint['priority'],
            'status' => $complaint['status'],
        ])->all();

        $categoryColors = ['#0f766e', '#f59e0b', '#d946ef', '#3b82f6', '#94a3b8'];
        $categoryGradientStops = [];
        $categoryOffset = 0;
        $categoryTotal = max(1, (int) ($totalComplaints ?? 0));
        foreach (collect($categoryCounts ?? [])->take(5) as $index => $categoryCount) {
            $nextOffset = $categoryOffset + (($categoryCount / $categoryTotal) * 100);
            $color = $categoryColors[count($categoryGradientStops) % count($categoryColors)];
            $categoryGradientStops[] = "{$color} {$categoryOffset}% {$nextOffset}%";
            $categoryOffset = $nextOffset;
        }
        $categoryGradient = $categoryGradientStops ? implode(', ', $categoryGradientStops) : '#e2e8f0 0 100%';
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

                        <form method="GET" action="{{ route('agent.dashboard') }}" class="flex flex-wrap items-end justify-end gap-3">
                            <label class="text-xs font-medium text-slate-600">
                                <span class="mb-1 block">Data inicial</span>
                                <input type="date" name="start_date" value="{{ $dashboardFilters['start_date'] ?? '' }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                            </label>
                            <label class="text-xs font-medium text-slate-600">
                                <span class="mb-1 block">Data final</span>
                                <input type="date" name="end_date" value="{{ $dashboardFilters['end_date'] ?? '' }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                            </label>
                            <label class="text-xs font-medium text-slate-600">
                                <span class="mb-1 block">Categoria</span>
                                <select name="category" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-200">
                                    <option value="">Todas as categorias</option>
                                    @foreach (($dashboardCategories ?? collect()) as $category)
                                        <option value="{{ $category }}" @selected(($dashboardFilters['category'] ?? '') === $category)>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <button type="submit" class="rounded-xl border border-slate-200 bg-slate-900 px-4 py-2 text-sm font-medium text-white">Filtrar</button>
                            <a href="{{ route('agent.dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700">Limpar</a>
                            <a href="{{ route('agent.dashboard.pdf', $dashboardFilters ?? []) }}" class="rounded-xl border border-slate-200 bg-slate-900 px-4 py-2 text-sm font-medium text-white">Exportar relatório</a>
                        </form>
                    </div>
                </section>

                <section class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                    @foreach ($metrics as $metric)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $metric['tone'] }}">
                                    @switch($metric['icon'])
                                        @case('clipboard')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6m-7 3h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2Zm2-3h2a2 2 0 0 1 2 2v1h-6V7a2 2 0 0 1 2-2Z"/>
                                            </svg>
                                            @break
                                        @case('folder')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z"/>
                                            </svg>
                                            @break
                                        @case('alert')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                                            </svg>
                                            @break
                                        @case('clock')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <circle cx="12" cy="12" r="8.5"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5v5l3 1.75"/>
                                            </svg>
                                            @break
                                        @case('search')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <circle cx="10.8" cy="10.8" r="5.8"/>
                                                <path stroke-linecap="round" d="m15.2 15.2 4 4"/>
                                            </svg>
                                            @break
                                        @case('check')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 12.5 4.2 4.2L19 7"/>
                                            </svg>
                                            @break
                                        @case('minus')
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" d="M5 12h14"/>
                                            </svg>
                                            @break
                                    @endswitch
                                </div>
                                <a href="{{ route('agent.dashboard', [...$dashboardBaseFilters, 'status' => $metric['filter'] ?? '']) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition hover:bg-slate-200" data-dashboard-filter="{{ $metric['filter'] ?? '' }}" aria-label="Filtrar denúncias: {{ $metric['label'] }}" aria-expanded="{{ ($dashboardFilters['status'] ?? '') === ($metric['filter'] ?? '') ? 'true' : 'false' }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/>
                                    </svg>
                                </a>
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
                            <div class="relative mx-auto h-52 w-52 rounded-full p-5" style="background: conic-gradient({{ $categoryGradient }})">
                                <div class="flex h-full w-full items-center justify-center rounded-full bg-white text-center shadow-inner">
                                    <div>
                                        <div class="text-4xl font-black text-slate-900">{{ number_format($totalComplaints ?? 0, 0, ',', '.') }}</div>
                                        <div class="text-sm text-slate-500">Total</div>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full space-y-3 text-sm text-slate-700">
                                @foreach (($categoryCounts ?? collect())->take(5) as $category => $count)
                                    <div class="flex items-center justify-between gap-3"><span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full" style="background-color: {{ $categoryColors[$loop->index % count($categoryColors)] }}"></span>{{ $category ?: 'Sem categoria' }}</span><span>{{ $count }} ({{ $totalComplaints ? round($count / $totalComplaints * 100) : 0 }}%)</span></div>
                                @endforeach
                            </div>
                        </div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-5 flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-slate-900">Evolução das denúncias</h2>
                            <span class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">Por mês</span>
                        </div>

                        @php
                            $dailyStatusCounts = collect($dailyStatusCounts ?? []);
                            $trendDates = $dailyStatusCounts->keys()->values();
                            $trendData = $dailyStatusCounts->all();
                            $trendSeries = [
                                'urgent' => ['label' => 'Urgente', 'color' => '#ef4444'],
                                'pending' => ['label' => 'Pendente', 'color' => '#fbbf24'],
                                'analysis' => ['label' => 'Em Análise', 'color' => '#0ea5e9'],
                                'in_progress' => ['label' => 'Em andamento', 'color' => '#2563eb'],
                                'resolved' => ['label' => 'Resolvida', 'color' => '#10b981'],
                            ];
                            $trendMax = max(1, (int) $dailyStatusCounts->flatMap(fn ($counts) => array_values($counts))->max());
                            $chartWidth = 720;
                            $chartHeight = 240;
                            $chartLeft = 38;
                            $chartRight = 12;
                            $chartTop = 18;
                            $chartBottom = 34;
                            $plotWidth = $chartWidth - $chartLeft - $chartRight;
                            $plotHeight = $chartHeight - $chartTop - $chartBottom;
                        @endphp
                        @if ($trendDates->isNotEmpty())
                            <div class="overflow-x-auto">
                                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="h-56 min-w-[560px] w-full" role="img" aria-label="Evolução das denúncias por status">
                                    @for ($grid = 0; $grid <= 4; $grid++)
                                        @php $gridY = $chartTop + (($plotHeight / 4) * $grid); @endphp
                                        <line x1="{{ $chartLeft }}" y1="{{ $gridY }}" x2="{{ $chartWidth - $chartRight }}" y2="{{ $gridY }}" stroke="#e2e8f0" stroke-width="1" />
                                        <text x="{{ $chartLeft - 8 }}" y="{{ $gridY + 4 }}" text-anchor="end" fill="#64748b" font-size="11">{{ round($trendMax - (($trendMax / 4) * $grid)) }}</text>
                                    @endfor

                                    @foreach ($trendSeries as $seriesKey => $series)
                                        @php
                                            $seriesPoints = [];
                                            foreach ($trendDates as $dateIndex => $date) {
                                                $x = $trendDates->count() === 1 ? $chartLeft + ($plotWidth / 2) : $chartLeft + (($plotWidth / ($trendDates->count() - 1)) * $dateIndex);
                                                $value = $trendData[$date][$seriesKey] ?? 0;
                                                $y = $chartTop + $plotHeight - (($value / $trendMax) * $plotHeight);
                                                $seriesPoints[] = round($x, 2).','.round($y, 2);
                                            }
                                        @endphp
                                        <polyline points="{{ implode(' ', $seriesPoints) }}" fill="none" stroke="{{ $series['color'] }}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                        @foreach ($trendDates as $dateIndex => $date)
                                            @php
                                                $x = $trendDates->count() === 1 ? $chartLeft + ($plotWidth / 2) : $chartLeft + (($plotWidth / ($trendDates->count() - 1)) * $dateIndex);
                                                $value = $trendData[$date][$seriesKey] ?? 0;
                                                $y = $chartTop + $plotHeight - (($value / $trendMax) * $plotHeight);
                                            @endphp
                                            <circle cx="{{ $x }}" cy="{{ $y }}" r="4.5" fill="white" stroke="{{ $series['color'] }}" stroke-width="3">
                                                <title>{{ $series['label'] }}: {{ $value }} em {{ $dashboardPeriod === 'year' ? $date : ($dashboardPeriod === 'month' ? \Illuminate\Support\Carbon::parse($date.'-01')->format('m/Y') : ($dashboardPeriod === 'week' ? 'semana de '.\Illuminate\Support\Carbon::parse($date)->format('d/m/Y') : \Illuminate\Support\Carbon::parse($date)->format('d/m/Y'))) }}</title>
                                            </circle>
                                        @endforeach
                                    @endforeach

                                    @foreach ($trendDates as $dateIndex => $date)
                                        @php $x = $trendDates->count() === 1 ? $chartLeft + ($plotWidth / 2) : $chartLeft + (($plotWidth / ($trendDates->count() - 1)) * $dateIndex); @endphp
                                        <text x="{{ $x }}" y="{{ $chartHeight - 8 }}" text-anchor="middle" fill="#64748b" font-size="11">{{ $dashboardPeriod === 'year' ? $date : ($dashboardPeriod === 'month' ? \Illuminate\Support\Carbon::parse($date.'-01')->format('m/Y') : ($dashboardPeriod === 'week' ? \Illuminate\Support\Carbon::parse($date)->format('d/m') : \Illuminate\Support\Carbon::parse($date)->format('d/m'))) }}</text>
                                    @endforeach
                                </svg>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-xs text-slate-600">
                                @foreach ($trendSeries as $series)
                                    <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-full" style="background-color: {{ $series['color'] }}"></span>{{ $series['label'] }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="flex h-56 items-center justify-center rounded-xl bg-slate-50 text-sm text-slate-500">Nenhuma denúncia no período selecionado.</div>
                        @endif
                    </article>
                </section>

                <section class="mt-8 grid gap-6 xl:grid-cols-3">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="mb-5 text-xl font-semibold text-slate-900">Denúncias por status</h2>
                        <div class="space-y-4 text-sm">
                            @foreach (($statusCounts ?? collect())->take(5) as $status => $count)
                                @php
                                    $statusChartColor = match ($status) {
                                        'Pendente' => '#fbbf24',
                                        'Em análise' => '#0ea5e9',
                                        'Concluído' => '#10b981',
                                        default => '#ef4444',
                                    };
                                @endphp
                                <div>
                                    <div class="mb-1 flex justify-between"><span>{{ $status }}</span><span class="font-semibold">{{ $count }} ({{ $totalComplaints ? round($count / $totalComplaints * 100) : 0 }}%)</span></div>
                                    <div class="h-2.5 rounded-full bg-slate-100"><div class="h-full rounded-full" style="width: {{ $totalComplaints ? round($count / $totalComplaints * 100) : 0 }}%; background-color: {{ $statusChartColor }}"></div></div>
                                </div>
                            @endforeach
                        </div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="mb-5 text-xl font-semibold text-slate-900">Denúncias por região</h2>
                        @php
                            $regionCounts = collect($districtCounts ?? [])->take(4);
                            $regionBubbles = $regionCounts->values();
                            $regionMax = max(1, (int) $regionBubbles->max());
                            $regionBubblePositions = [
                                ['left' => '12px', 'top' => '28px'],
                                ['left' => '48px', 'top' => '0'],
                                ['left' => '100px', 'top' => '36px'],
                                ['left' => '58px', 'top' => '58px'],
                            ];
                            $regionBubbleColors = ['bg-emerald-200/70', 'bg-emerald-300/80', 'bg-emerald-100/80', 'bg-teal-200/70'];
                        @endphp
                        <div class="flex min-h-40 items-center justify-center pt-5">
                            @if ($regionCounts->isNotEmpty())
                                <div class="relative h-32 w-44">
                                    @foreach ($regionBubbles as $index => $count)
                                        @php
                                            $bubbleSize = 48 + (int) round(($count / $regionMax) * 48);
                                            $bubblePosition = $regionBubblePositions[$index] ?? ['left' => '48px', 'top' => '28px'];
                                        @endphp
                                        <div class="absolute flex items-center justify-center rounded-full {{ $regionBubbleColors[$index % count($regionBubbleColors)] }} text-xs font-semibold text-emerald-900/70 shadow-sm" style="left: {{ $bubblePosition['left'] }}; top: {{ $bubblePosition['top'] }}; width: {{ $bubbleSize }}px; height: {{ $bubbleSize }}px;" title="{{ $count }} denúncia(s)">
                                            {{ $count }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-500">Nenhuma denúncia por região no filtro selecionado.</p>
                            @endif
                        </div>
                        <div class="mt-5 space-y-3 text-sm text-slate-700">
                            @forelse ($regionCounts as $district => $count)
                                <div class="flex items-center justify-between"><span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-emerald-400"></span>{{ $district }}</span><span>{{ $count }}</span></div>
                            @empty
                                <p class="text-sm text-slate-500">Nenhuma região encontrada.</p>
                            @endforelse
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
                            <h2 class="text-xl font-semibold text-slate-900" data-dashboard-filter-title>Ocorrências recentes</h2>
                            <a href="{{ route('agent.dashboard', $dashboardBaseFilters) }}" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700" data-dashboard-filter="all" data-dashboard-show-all aria-expanded="{{ empty($dashboardFilters['status'] ?? '') ? 'true' : 'false' }}">Ver todas</a>
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
                            <tbody data-dashboard-complaint-rows>
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
                                            @php
                                                $statusTone = match ($complaint['status']) {
                                                    'Concluído' => 'bg-emerald-100 text-emerald-700',
                                                    'Em análise' => 'bg-sky-100 text-sky-700',
                                                    'Em andamento' => 'bg-blue-100 text-blue-700',
                                                    'Rejeitado' => 'bg-rose-100 text-rose-700',
                                                    default => 'bg-amber-100 text-amber-700',
                                                };
                                            @endphp
                                            <span class="rounded-full px-2 py-1 text-xs font-medium {{ $statusTone }}">
                                                {{ $complaint['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <a href="{{ route('complaints.show', ['publicId' => $complaint['publicId']]) }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Ver</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="hidden py-8 text-center text-sm text-slate-500" data-dashboard-empty>Nenhuma denúncia encontrada no período selecionado.</p>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <script type="application/json" id="dashboard-complaints-data">@json($dashboardComplaints ?? [])</script>
</x-app-layout>
