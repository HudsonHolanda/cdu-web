<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Dashboard de denúncias</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #17251d; font-size: 10px; }
        h1 { color: #1d3b34; margin: 0 0 4px; }
        h2 { color: #1d3b34; font-size: 14px; margin: 20px 0 8px; }
        .muted { color: #68756d; }
        .summary { display: flex; gap: 8px; margin: 16px 0; }
        .card { border: 1px solid #dce5de; padding: 8px; flex: 1; }
        .card strong { display: block; font-size: 18px; margin-top: 3px; }
        .columns { display: flex; gap: 14px; }
        .column { flex: 1; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #1d3b34; color: white; text-align: left; }
        th, td { border: 1px solid #dce5de; padding: 5px; }
        tr:nth-child(even) { background: #f5f7f5; }
        .bar { height: 7px; background: #e8eee9; }
        .fill { height: 7px; background: #10b981; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    @php
        $pdfCategoryColors = ['#0f766e', '#f59e0b', '#d946ef', '#3b82f6', '#94a3b8'];
        $pdfCategorySegments = [];
        $pdfCategoryOffset = 0;
        $pdfCategoryTotal = max(1, (int) $summary['total']);
        $pdfCircleLength = 439.82;
        foreach (collect($categoryCounts)->take(5) as $categoryIndex => $categoryCount) {
            $segmentLength = ($categoryCount / $pdfCategoryTotal) * $pdfCircleLength;
            $pdfCategorySegments[] = [
                'color' => $pdfCategoryColors[count($pdfCategorySegments) % count($pdfCategoryColors)],
                'length' => $segmentLength,
                'offset' => $pdfCategoryOffset,
            ];
            $pdfCategoryOffset += $segmentLength;
        }

        $pdfTrendSeries = [
            'urgent' => ['label' => 'Urgente', 'color' => '#ef4444'],
            'pending' => ['label' => 'Pendente', 'color' => '#fbbf24'],
            'analysis' => ['label' => 'Em análise', 'color' => '#0ea5e9'],
            'in_progress' => ['label' => 'Em andamento', 'color' => '#2563eb'],
            'resolved' => ['label' => 'Resolvida', 'color' => '#10b981'],
        ];
        $pdfTrendData = collect($dailyStatusCounts)->all();
        $pdfTrendDates = collect($dailyStatusCounts)->keys()->values();
        $pdfTrendMax = max(1, (int) collect($dailyStatusCounts)->flatMap(fn ($counts) => array_values($counts))->max());
        $pdfChartWidth = 720;
        $pdfChartHeight = 240;
        $pdfChartLeft = 38;
        $pdfChartTop = 18;
        $pdfPlotWidth = 670;
        $pdfPlotHeight = 188;

        $pdfCategorySvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="190" height="190">';
        $pdfCategorySvg .= '<circle cx="100" cy="100" r="70" fill="none" stroke="#e2e8f0" stroke-width="28" />';
        foreach ($pdfCategorySegments as $segment) {
            $pdfCategorySvg .= sprintf(
                '<circle cx="100" cy="100" r="70" fill="none" stroke="%s" stroke-width="28" stroke-dasharray="%s %s" stroke-dashoffset="-%s" transform="rotate(-90 100 100)" />',
                $segment['color'],
                $segment['length'],
                $pdfCircleLength - $segment['length'],
                $segment['offset']
            );
        }
        $pdfCategorySvg .= sprintf('<text x="100" y="96" text-anchor="middle" font-size="28" font-weight="bold" fill="#17251d">%s</text>', $summary['total']);
        $pdfCategorySvg .= '<text x="100" y="115" text-anchor="middle" font-size="11" fill="#68756d">Total</text></svg>';

        $pdfTrendSvg = sprintf('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %s %s" width="720" height="240">', $pdfChartWidth, $pdfChartHeight);
        for ($grid = 0; $grid <= 4; $grid++) {
            $pdfGridY = $pdfChartTop + (($pdfPlotHeight / 4) * $grid);
            $pdfGridLabel = round($pdfTrendMax - (($pdfTrendMax / 4) * $grid));
            $pdfTrendSvg .= sprintf('<line x1="%s" y1="%s" x2="708" y2="%s" stroke="#e2e8f0" stroke-width="1" /><text x="30" y="%s" text-anchor="end" fill="#64748b" font-size="10">%s</text>', $pdfChartLeft, $pdfGridY, $pdfGridY, $pdfGridY + 4, $pdfGridLabel);
        }
        foreach ($pdfTrendSeries as $pdfSeriesKey => $pdfSeries) {
            $pdfSeriesPoints = [];
            foreach ($pdfTrendDates as $pdfDateIndex => $pdfDate) {
                $pdfX = $pdfTrendDates->count() === 1 ? $pdfChartLeft + ($pdfPlotWidth / 2) : $pdfChartLeft + (($pdfPlotWidth / ($pdfTrendDates->count() - 1)) * $pdfDateIndex);
                $pdfValue = $pdfTrendData[$pdfDate][$pdfSeriesKey] ?? 0;
                $pdfY = $pdfChartTop + $pdfPlotHeight - (($pdfValue / $pdfTrendMax) * $pdfPlotHeight);
                $pdfSeriesPoints[] = round($pdfX, 2).','.round($pdfY, 2);
            }
            if ($pdfSeriesPoints) {
                $pdfTrendSvg .= sprintf('<polyline points="%s" fill="none" stroke="%s" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />', implode(' ', $pdfSeriesPoints), $pdfSeries['color']);
                foreach ($pdfTrendDates as $pdfDateIndex => $pdfDate) {
                    $pdfX = $pdfTrendDates->count() === 1 ? $pdfChartLeft + ($pdfPlotWidth / 2) : $pdfChartLeft + (($pdfPlotWidth / ($pdfTrendDates->count() - 1)) * $pdfDateIndex);
                    $pdfValue = $pdfTrendData[$pdfDate][$pdfSeriesKey] ?? 0;
                    $pdfY = $pdfChartTop + $pdfPlotHeight - (($pdfValue / $pdfTrendMax) * $pdfPlotHeight);
                    $pdfTrendSvg .= sprintf('<circle cx="%s" cy="%s" r="4" fill="white" stroke="%s" stroke-width="3" />', $pdfX, $pdfY, $pdfSeries['color']);
                }
            }
        }
        $pdfTrendSvg .= '</svg>';
    @endphp

    <h1>Dashboard de denúncias</h1>
    <p class="muted">
        Gerado em {{ now()->format('d/m/Y H:i') }}.
        Período: {{ ($filters['start_date'] ?? null) ? \Illuminate\Support\Carbon::parse($filters['start_date'])->format('d/m/Y') : 'início' }}
        até {{ ($filters['end_date'] ?? null) ? \Illuminate\Support\Carbon::parse($filters['end_date'])->format('d/m/Y') : 'hoje' }}.
    </p>

    <div class="summary">
        <div class="card">Total<strong>{{ $summary['total'] }}</strong></div>
        <div class="card">Pendentes<strong>{{ $summary['pending'] }}</strong></div>
        <div class="card">Em análise<strong>{{ $summary['analysis'] }}</strong></div>
        <div class="card">Em andamento<strong>{{ $summary['in_progress'] }}</strong></div>
        <div class="card">Resolvidas<strong>{{ $summary['resolved'] }}</strong></div>
        <div class="card">Urgentes<strong>{{ $summary['urgent'] }}</strong></div>
        <div class="card">Normais<strong>{{ $summary['normal'] }}</strong></div>
    </div>

    <div class="columns">
        <div class="column">
            <h2>Denúncias por categoria</h2>
            <img src="data:image/svg+xml;base64,{{ base64_encode($pdfCategorySvg) }}" width="190" height="190" alt="Gráfico de denúncias por categoria">
        </div>
        <div class="column" style="flex: 2">
            <h2>Evolução das denúncias</h2>
            @if ($pdfTrendDates->isNotEmpty())
                <img src="data:image/svg+xml;base64,{{ base64_encode($pdfTrendSvg) }}" width="720" height="210" alt="Gráfico de evolução das denúncias">
            @else
                <p class="muted">Nenhuma evolução encontrada no período.</p>
            @endif
        </div>
    </div>

    <p class="muted">
        Legenda: <span style="color:#ef4444">● Urgente</span>&nbsp;&nbsp;
        <span style="color:#fbbf24">● Pendente</span>&nbsp;&nbsp;
        <span style="color:#0ea5e9">● Em análise</span>&nbsp;&nbsp;
        <span style="color:#2563eb">● Em andamento</span>&nbsp;&nbsp;
        <span style="color:#10b981">● Resolvida</span>
    </p>

    <div class="columns">
        <div class="column">
            <h2>Denúncias por categoria</h2>
            <table>
                <thead><tr><th>Categoria</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse ($categoryCounts as $category => $count)
                        <tr><td>{{ $category ?: 'Sem categoria' }}</td><td>{{ $count }}</td></tr>
                    @empty
                        <tr><td colspan="2">Nenhuma categoria encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="column">
            <h2>Denúncias por status</h2>
            <table>
                <thead><tr><th>Status</th><th>Total</th><th>Percentual</th></tr></thead>
                <tbody>
                    @forelse ($statusCounts as $status => $count)
                        <tr>
                            <td>{{ $status }}</td>
                            <td>{{ $count }}</td>
                            <td>{{ $summary['total'] ? round(($count / $summary['total']) * 100) : 0 }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">Nenhum status encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="column">
            <h2>Denúncias por região</h2>
            <table>
                <thead><tr><th>Bairro</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse ($districtCounts as $district => $count)
                        <tr><td>{{ $district ?: 'Não informado' }}</td><td>{{ $count }}</td></tr>
                    @empty
                        <tr><td colspan="2">Nenhuma região encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <h2>Evolução diária por status</h2>
    <table>
        <thead><tr><th>Data</th><th>Urgente</th><th>Pendente</th><th>Em análise</th><th>Em andamento</th><th>Resolvida</th></tr></thead>
        <tbody>
            @forelse ($dailyStatusCounts as $date => $counts)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</td>
                    <td>{{ $counts['urgent'] ?? 0 }}</td>
                    <td>{{ $counts['pending'] ?? 0 }}</td>
                    <td>{{ $counts['analysis'] ?? 0 }}</td>
                    <td>{{ $counts['in_progress'] ?? 0 }}</td>
                    <td>{{ $counts['resolved'] ?? 0 }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Nenhuma evolução encontrada no período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="page-break">Ocorrências recentes</h2>
    <p class="muted">Ocorrências do período selecionado na Dashboard.</p>
    <table>
        <thead><tr><th>ID</th><th>Ocorrência</th><th>Localização</th><th>Data</th><th>Prioridade</th><th>Status</th></tr></thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row['id'] }}</td>
                    <td>{{ $row['title'] }}</td>
                    <td>{{ $row['district'] }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['priority'] }}</td>
                    <td>{{ $row['status'] }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Nenhuma ocorrência encontrada no período selecionado.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
