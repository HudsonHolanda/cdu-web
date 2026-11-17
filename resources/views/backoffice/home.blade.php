<x-app-layout>
    <x-slot name="header"><h2>Bom dia, {{ auth()->user()->isAdmin() ? 'Administrador' : 'Agente' }}!</h2><p>Aqui está o panorama geral das ocorrências da cidade.</p></x-slot>
    <div class="cdu-dashboard-page">
        <div class="cdu-dashboard-grid">
            @foreach ([['Pendente', $pending, '▢'], ['Em análise', $analysis, '▤'], ['Resolvidas', $resolved, '✓'], ['Urgentes', $urgent, '!']] as [$label, $value, $icon])
                <article class="cdu-stat-card"><span class="cdu-stat-icon">{{ $icon }}</span><div><strong>{{ $value }}</strong><span>{{ $label }}</span></div></article>
            @endforeach
        </div>
        <div class="cdu-dashboard-columns">
            <section class="cdu-panel">
                <div class="cdu-panel-header"><h2>Ocorrências recentes</h2><a class="cdu-panel-link" href="{{ route('complaints.index') }}" wire:navigate>Ver todas</a></div>
                <div class="overflow-x-auto"><table class="cdu-backoffice-table"><thead><tr><th>ID</th><th>Ocorrência</th><th>Localização</th><th>Data</th><th>Status</th></tr></thead><tbody>
                    @forelse ($complaints->take(5) as $complaint)<tr><td>{{ str_replace('DEN-2026-', '#', $complaint['complaintId']) }}</td><td>{{ $complaint['title'] }}</td><td>{{ $complaint['location']['district'] ?? '—' }}</td><td>{{ \Illuminate\Support\Carbon::parse($complaint['createdAt'])->format('d/m/Y') }}</td><td class="cdu-status {{ in_array($complaint['status'], ['Resolvida', 'Concluída']) ? 'is-done' : '' }}">{{ $complaint['status'] }}</td></tr>@empty<tr><td colspan="5">Nenhuma ocorrência registrada.</td></tr>@endforelse
                </tbody></table></div>
            </section>
            <section class="cdu-panel"><div class="cdu-panel-header"><h2>Mapa de ocorrências</h2><span class="cdu-panel-link">{{ $total }} registros</span></div><div class="cdu-map-placeholder">Mapa de ocorrências</div></section>
        </div>
    </div>
</x-app-layout>
