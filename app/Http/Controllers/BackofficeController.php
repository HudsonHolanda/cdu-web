<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class BackofficeController extends Controller
{
    public function agentOverview(): View
    {
        $complaints = $this->complaints();

        return view('agents.overview', $this->panelData($complaints));
    }

    public function agentDashboard(): View
    {
        $complaints = $this->complaints();

        return view('agents.dashboard', $this->panelData($complaints));
    }

    public function agentMap(): View
    {
        $complaints = $this->complaints();

        return view('agents.mapa', [
            'mapComplaints' => $complaints->map(fn (array $complaint): array => [
                'id' => $complaint['id'],
                'title' => $complaint['title'],
                'address' => $complaint['location']['address'],
                'district' => $complaint['location']['district'],
                'status' => $complaint['status'],
                'priority' => $complaint['priority'],
                'lat' => $complaint['location']['latitude'],
                'lng' => $complaint['location']['longitude'],
                'color' => $complaint['color'],
                'description' => $complaint['description'],
                'date' => $complaint['date'],
            ])->values(),
        ]);
    }

    public function agentReports(Request $request): View
    {
        $query = $this->reportQuery($request);
        $complaints = $query->get();

        return view('agents.relatorios', [
            'reportRows' => $this->reportRows($complaints),
            'reportSummary' => [
                'total' => $complaints->count(),
                'urgent' => $complaints->where('high_priority', true)->count(),
                'resolved' => $complaints->where('status', ComplaintStatus::RESOLVED)->count(),
            ],
            'reportCategories' => Complaint::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'reportFilters' => $request->only(['start_date', 'end_date', 'status', 'category', 'priority']),
        ]);
    }

    public function agentReportPdf(Request $request)
    {
        $complaints = $this->reportQuery($request)->get();

        return Pdf::loadView('agents.relatorios-pdf', [
            'rows' => $this->reportRows($complaints),
            'summary' => [
                'total' => $complaints->count(),
                'urgent' => $complaints->where('high_priority', true)->count(),
                'resolved' => $complaints->where('status', ComplaintStatus::RESOLVED)->count(),
            ],
            'filters' => $request->only(['start_date', 'end_date', 'status', 'category', 'priority']),
        ])->setPaper('a4', 'landscape')->download('relatorio-denuncias-'.now()->format('Y-m-d').'.pdf');
    }

    public function home(): View
    {
        $complaints = $this->complaints();

        return view('backoffice.home', $this->metrics($complaints));
    }

    public function dashboard(): View
    {
        $complaints = $this->complaints();

        return view('backoffice.dashboard', $this->metrics($complaints));
    }

    /** @return array<string, mixed> */
    private function complaints(): Collection
    {
        $items = [];

        foreach (Complaint::query()->latest()->get() as $complaint) {
            $items[] = [
                'id' => '#'.str_pad((string) $complaint->complaint_id, 3, '0', STR_PAD_LEFT),
                'complaintId' => 'DEN-'.now()->format('Y').'-'.$complaint->complaint_id,
                'category' => $complaint->category,
                'title' => $complaint->title,
                'description' => $complaint->description,
                'status' => $complaint->status?->label() ?? 'Pendente',
                'highPriority' => (bool) $complaint->high_priority,
                'priority' => $complaint->high_priority ? 'Urgente' : 'Normal',
                'createdAt' => $complaint->created_at?->toIso8601String(),
                'date' => $complaint->created_at?->format('d/m/Y'),
                'color' => $this->statusColor($complaint),
                'location' => [
                    'latitude' => (float) $complaint->latitude,
                    'longitude' => (float) $complaint->longitude,
                    'address' => $complaint->address,
                    'district' => $complaint->district,
                ],
                '_status' => $complaint->status,
            ];
        }

        return collect($items);
    }

    /** @return array<string, mixed> */
    private function metrics(Collection $complaints): array
    {
        $statusCount = fn (ComplaintStatus $status): int => $complaints->where('_status', $status)->count();

        return [
            'complaints' => $complaints->values(),
            'total' => $complaints->count(),
            'pending' => $statusCount(ComplaintStatus::PENDING),
            'analysis' => $statusCount(ComplaintStatus::UNDER_REVIEW),
            'resolved' => $statusCount(ComplaintStatus::RESOLVED),
            'urgent' => $complaints->where('highPriority', true)->count(),
            'categories' => $complaints->groupBy('category')->map->count()->sortDesc(),
        ];
    }

    /** @return array<string, mixed> */
    private function panelData(Collection $complaints): array
    {
        $statusCount = fn (ComplaintStatus $status): int => $complaints->where('_status', $status)->count();

        return [
            'complaints' => $complaints->values(),
            'panelMetrics' => [
                ['label' => 'Pendentes', 'value' => $statusCount(ComplaintStatus::PENDING), 'icon' => 'clipboard', 'accent' => 'text-amber-500'],
                ['label' => 'Em Análise', 'value' => $statusCount(ComplaintStatus::UNDER_REVIEW), 'icon' => 'folder', 'accent' => 'text-sky-500'],
                ['label' => 'Resolvidas', 'value' => $statusCount(ComplaintStatus::RESOLVED), 'icon' => 'check', 'accent' => 'text-emerald-500'],
                ['label' => 'Urgentes', 'value' => $complaints->where('highPriority', true)->count(), 'icon' => 'alert', 'accent' => 'text-rose-500'],
            ],
            'panelComplaints' => $complaints->take(5)->values(),
            'mapMarkers' => $complaints->map(fn (array $complaint): array => [
                'coords' => [$complaint['location']['latitude'], $complaint['location']['longitude']],
                'color' => $complaint['color'],
                'label' => $complaint['status'],
            ])->values()->all(),
            'categoryCounts' => $complaints->groupBy('category')->map->count()->sortDesc(),
            'statusCounts' => $complaints->groupBy('status')->map->count()->sortDesc(),
            'districtCounts' => $complaints->pluck('location.district')->filter()->countBy()->sortDesc(),
            'totalComplaints' => $complaints->count(),
        ];
    }

    private function statusColor(Complaint $complaint): string
    {
        return $complaint->high_priority ? '#ef4444' : match ($complaint->status) {
            ComplaintStatus::RESOLVED => '#22c55e',
            ComplaintStatus::UNDER_REVIEW, ComplaintStatus::IN_PROGRESS => '#0ea5e9',
            default => '#f59e0b',
        };
    }

    private function reportQuery(Request $request): Builder
    {
        return Complaint::query()
            ->when($request->filled('start_date'), fn (Builder $query) => $query->whereDate('created_at', '>=', $request->date('start_date')))
            ->when($request->filled('end_date'), fn (Builder $query) => $query->whereDate('created_at', '<=', $request->date('end_date')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', (int) $request->input('status')))
            ->when($request->filled('category'), fn (Builder $query) => $query->where('category', $request->input('category')))
            ->when($request->input('priority') === 'urgent', fn (Builder $query) => $query->where('high_priority', true))
            ->when($request->input('priority') === 'normal', fn (Builder $query) => $query->where('high_priority', false))
            ->latest();
    }

    /** @return Collection<int, array<string, mixed>> */
    private function reportRows(Collection $complaints): Collection
    {
        return $complaints->map(fn (Complaint $complaint): array => [
            'id' => '#'.str_pad((string) $complaint->complaint_id, 3, '0', STR_PAD_LEFT),
            'title' => $complaint->title,
            'category' => $complaint->category ?: 'Sem categoria',
            'district' => $complaint->district ?: 'Não informado',
            'status' => $complaint->status?->label() ?? 'Pendente',
            'priority' => $complaint->high_priority ? 'Urgente' : 'Normal',
            'date' => $complaint->created_at?->format('d/m/Y H:i'),
        ])->values();
    }
}
