<x-app-layout>
    <x-slot name="header"><h2>Dashboard</h2><p>Visão geral das ocorrências registradas na cidade.</p></x-slot>
    <div class="cdu-dashboard-page">
        <div class="cdu-dashboard-grid">
            @foreach ([['Total de denúncias', $total], ['Pendentes', $pending], ['Em análise', $analysis], ['Resolvidas', $resolved]] as [$label, $value])
                <article class="cdu-stat-card"><span class="cdu-stat-icon">▣</span><div><strong>{{ number_format($value, 0, ',', '.') }}</strong><span>{{ $label }}</span></div></article>
            @endforeach
        </div>
        <div class="cdu-dashboard-columns">
            <section class="cdu-panel"><div class="cdu-panel-header"><h2>Denúncias por categoria</h2><span class="cdu-panel-link">{{ $total }} total</span></div><div class="cdu-chart">@foreach ($categories->take(8) as $category => $count)<i style="height: {{ max(18, round(($count / max(1, $total)) * 100)) }}%" title="{{ $category }}: {{ $count }}"></i>@endforeach</div><table class="cdu-backoffice-table"><tbody>@foreach ($categories->take(5) as $category => $count)<tr><td>{{ $category ?: 'Outros' }}</td><td>{{ $count }}</td></tr>@endforeach</tbody></table></section>
            <section class="cdu-panel"><div class="cdu-panel-header"><h2>Resumo por status</h2></div><table class="cdu-backoffice-table"><tbody><tr><td>Pendentes</td><td>{{ $pending }}</td></tr><tr><td>Em análise</td><td>{{ $analysis }}</td></tr><tr><td>Resolvidas</td><td>{{ $resolved }}</td></tr><tr><td>Urgentes</td><td>{{ $urgent }}</td></tr></tbody></table></section>
        </div>
    </div>
</x-app-layout>
