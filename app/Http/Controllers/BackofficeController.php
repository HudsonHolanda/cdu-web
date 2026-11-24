<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BackofficeController extends Controller
{
    public function agentOverview(Request $request): View
    {
        $complaints = $this->complaints();
        $panelData = $this->panelData($complaints);

        return view('agents.overview', $panelData);
    }

    public function agentDashboard(Request $request): View
    {
        $allComplaints = $this->complaints();
        $complaints = $this->filterDashboardComplaints($allComplaints, $request);
        $trendPeriod = $this->dashboardTrendPeriod($request);
        $dashboardFilters = $this->dashboardFilters($request);

        $panelData = $this->panelData($complaints, $trendPeriod);
        $cardComplaints = $this->filterDashboardComplaints($allComplaints, $request, true);
        $panelData['panelMetrics'] = $this->panelData($cardComplaints)['panelMetrics'];

        return view('agents.dashboard', [
            ...$panelData,
            'dashboardFilters' => $dashboardFilters,
            'dashboardCategories' => collect([
                'Buraco na via',
                'Iluminação pública',
                'Lixo acumulado',
                'Vazamento de água',
                'Pragas e animais',
                'Outros',
            ])->merge($allComplaints->pluck('category'))->filter()->unique()->values(),
        ]);
    }

    public function agentDashboardPdf(Request $request)
    {
        $complaints = $this->filterDashboardComplaints($this->complaints(), $request);
        $trendPeriod = $this->dashboardTrendPeriod($request);
        $panelData = $this->panelData($complaints, $trendPeriod);
        $dashboardFilters = $this->dashboardFilters($request);

        return Pdf::loadView('agents.dashboard-pdf', [
            'rows' => $this->dashboardRows($complaints),
            'summary' => [
                'total' => $complaints->count(),
                'pending' => $complaints->where('_status', ComplaintStatus::PENDING)->count(),
                'analysis' => $complaints->where('_status', ComplaintStatus::UNDER_REVIEW)->count(),
                'in_progress' => $complaints->where('_status', ComplaintStatus::IN_PROGRESS)->count(),
                'normal' => $complaints->where('highPriority', false)->count(),
                'resolved' => $complaints->where('_status', ComplaintStatus::RESOLVED)->count(),
                'urgent' => $complaints->where('highPriority', true)->count(),
            ],
            'categoryCounts' => $panelData['categoryCounts'],
            'statusCounts' => $panelData['statusCounts'],
            'districtCounts' => $panelData['districtCounts'],
            'dailyStatusCounts' => $panelData['dailyStatusCounts'],
            'filters' => $dashboardFilters,
            'trendPeriod' => $trendPeriod,
        ])->setPaper('a4', 'landscape')->download('dashboard-denuncias-'.now()->format('Y-m-d').'.pdf');
    }

    public function agentMap(): View
    {
        $complaints = $this->complaints();

        return view('agents.mapa', [
            'mapComplaints' => $complaints->map(fn (array $complaint): array => [
                'id' => $complaint['id'],
                'publicId' => $complaint['publicId'],
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

    public function agentComplaintsTable(Request $request): JsonResponse
    {
        $query = Complaint::query();
        $context = $request->input('context', 'dashboard');

        if ($context === 'dashboard') {
            $query
                ->when($request->filled('start_date'), fn (Builder $builder) => $builder->whereDate('created_at', '>=', $request->input('start_date')))
                ->when($request->filled('end_date'), fn (Builder $builder) => $builder->whereDate('created_at', '<=', $request->input('end_date')));
        }

        $this->applyTableFilter($query, $request->input('status'), $request->input('category'));

        $recordsTotal = (clone $query)->count();
        $search = trim((string) $request->input('search.value', ''));

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();
        $orderColumns = [
            0 => 'complaint_id',
            1 => 'title',
            2 => 'district',
            3 => 'created_at',
            4 => 'high_priority',
            5 => 'status',
        ];
        $orderColumn = $orderColumns[(int) $request->input('order.0.column', 3)] ?? 'created_at';
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $length = (int) $request->input('length', 100);
        $length = $length < 1 ? 100 : min($length, 100);
        $start = max(0, (int) $request->input('start', 0));

        $rows = $query
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get()
            ->map(fn (Complaint $complaint): array => [
                'id' => '#'.str_pad((string) $complaint->complaint_id, 3, '0', STR_PAD_LEFT),
                'publicId' => $complaint->public_id,
                'title' => $complaint->title,
                'district' => $complaint->district ?: 'Não informado',
                'date' => $complaint->created_at?->format('d/m/Y'),
                'priority' => $complaint->high_priority ? 'Urgente' : 'Normal',
                'status' => $complaint->status?->label() ?? 'Pendente',
            ])->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function agentReports(Request $request): View
    {
        $query = $this->reportQuery($request);

        return view('agents.relatorios', [
            'reportSummary' => [
                'total' => (clone $query)->count(),
                'urgent' => (clone $query)->where('high_priority', true)->count(),
                'resolved' => (clone $query)->where('status', ComplaintStatus::RESOLVED)->count(),
            ],
            'reportCategories' => Complaint::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'reportFilters' => $request->only(['start_date', 'end_date', 'status', 'category', 'priority']),
        ]);
    }

    public function agentReportsTable(Request $request): JsonResponse
    {
        $query = $this->reportQuery($request);
        $recordsTotal = (clone $query)->count();
        $search = trim((string) $request->input('search.value', ''));

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();
        $orderColumns = [
            0 => 'complaint_id',
            1 => 'title',
            2 => 'category',
            3 => 'district',
            4 => 'status',
            5 => 'high_priority',
            7 => 'created_at',
        ];
        $orderColumn = $orderColumns[(int) $request->input('order.0.column', 7)] ?? 'created_at';
        $orderDirection = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $length = max(1, min((int) $request->input('length', 10), 100));
        $start = max(0, (int) $request->input('start', 0));

        $rows = $query
            ->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get()
            ->map(fn (Complaint $complaint): array => $this->reportRows(collect([$complaint]))->first())
            ->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
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
                'publicId' => $complaint->public_id,
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
            'in_progress' => $statusCount(ComplaintStatus::IN_PROGRESS),
            'normal' => $complaints->where('highPriority', false)->count(),
            'resolved' => $statusCount(ComplaintStatus::RESOLVED),
            'urgent' => $complaints->where('highPriority', true)->count(),
            'categories' => $complaints->groupBy('category')->map->count()->sortDesc(),
        ];
    }

    private function filterDashboardComplaints(Collection $complaints, Request $request, bool $ignoreStatus = false): Collection
    {
        $allDates = $request->boolean('all_dates');
        $startDate = $allDates ? null : ($request->input('start_date') ?: now()->startOfYear()->toDateString());
        $endDate = $allDates ? null : ($request->input('end_date') ?: now()->toDateString());
        $category = $request->input('category');
        $status = $ignoreStatus ? null : $request->input('status');

        return $complaints->filter(function (array $complaint) use ($startDate, $endDate, $category, $status): bool {
            $date = substr((string) $complaint['createdAt'], 0, 10);
            $matchesStatus = match ($status) {
                'pending' => $complaint['_status'] === ComplaintStatus::PENDING,
                'analysis' => $complaint['_status'] === ComplaintStatus::UNDER_REVIEW,
                'in_progress' => $complaint['_status'] === ComplaintStatus::IN_PROGRESS,
                'resolved' => $complaint['_status'] === ComplaintStatus::RESOLVED,
                'urgent' => (bool) $complaint['highPriority'],
                'normal' => ! $complaint['highPriority'],
                default => true,
            };

            return (!$startDate || $date >= $startDate)
                && (!$endDate || $date <= $endDate)
                && (!$category || ($complaint['category'] ?? null) === $category)
                && $matchesStatus;
        })->values();
    }

    private function dashboardRows(Collection $complaints): Collection
    {
        return $complaints->map(fn (array $complaint): array => [
            'id' => $complaint['id'],
            'title' => $complaint['title'],
            'category' => $complaint['category'] ?: 'Sem categoria',
            'district' => $complaint['location']['district'] ?: 'Não informado',
            'status' => $complaint['status'],
            'priority' => $complaint['priority'],
            'date' => $complaint['date'],
        ])->values();
    }

    /** @return array<string, mixed> */
    private function panelData(Collection $complaints, string $trendPeriod = 'day'): array
    {
        $statusCount = fn (ComplaintStatus $status): int => $complaints->where('_status', $status)->count();

        return [
            'complaints' => $complaints->values(),
            'panelMetrics' => [
                ['label' => 'Pendentes', 'value' => $statusCount(ComplaintStatus::PENDING), 'icon' => 'clipboard', 'accent' => 'text-amber-500', 'filter' => 'pending'],
                ['label' => 'Em Análise', 'value' => $statusCount(ComplaintStatus::UNDER_REVIEW), 'icon' => 'folder', 'accent' => 'text-sky-500', 'filter' => 'analysis'],
                ['label' => 'Em andamento', 'value' => $statusCount(ComplaintStatus::IN_PROGRESS), 'icon' => 'clock', 'accent' => 'text-blue-500', 'filter' => 'in_progress'],
                ['label' => 'Resolvidas', 'value' => $statusCount(ComplaintStatus::RESOLVED), 'icon' => 'check', 'accent' => 'text-emerald-500', 'filter' => 'resolved'],
                ['label' => 'Urgentes', 'value' => $complaints->where('highPriority', true)->count(), 'icon' => 'alert', 'accent' => 'text-rose-500', 'filter' => 'urgent'],
                ['label' => 'Normais', 'value' => $complaints->where('highPriority', false)->count(), 'icon' => 'minus', 'accent' => 'text-slate-500', 'filter' => 'normal'],
            ],
            'panelComplaints' => $complaints->take(5)->values(),
            'mapMarkers' => $complaints->map(fn (array $complaint): array => [
                'coords' => [$complaint['location']['latitude'], $complaint['location']['longitude']],
                'color' => $complaint['color'],
                'label' => $complaint['status'],
                'filters' => array_values(array_filter([
                    $complaint['highPriority'] ? 'urgent' : 'normal',
                    match ($complaint['_status']) {
                        ComplaintStatus::PENDING => 'pending',
                        ComplaintStatus::UNDER_REVIEW => 'analysis',
                        ComplaintStatus::IN_PROGRESS => 'in_progress',
                        ComplaintStatus::RESOLVED => 'resolved',
                        default => null,
                    },
                ])),
            ])->values()->all(),
            'categoryCounts' => $complaints->groupBy('category')->map->count()->sortDesc(),
            'statusCounts' => $complaints->groupBy('status')->map->count()->sortDesc(),
            'districtCounts' => $complaints->pluck('location.district')->filter()->countBy()->sortDesc(),
            'dailyCounts' => $complaints
                ->filter(fn (array $complaint): bool => filled($complaint['createdAt']))
                ->groupBy(fn (array $complaint): string => substr($complaint['createdAt'], 0, 10))
                ->map->count()
                ->sortKeys(),
            'dailyStatusCounts' => $this->trendStatusCounts($complaints, $trendPeriod),
            'totalComplaints' => $complaints->count(),
        ];
    }

    private function applyTableFilter(Builder $query, ?string $status, ?string $category): void
    {
        $query
            ->when($category, fn (Builder $builder) => $builder->where('category', $category))
            ->when($status === 'urgent', fn (Builder $builder) => $builder->where('high_priority', true))
            ->when($status === 'normal', fn (Builder $builder) => $builder->where('high_priority', false))
            ->when(in_array($status, ['pending', 'analysis', 'in_progress', 'resolved'], true), function (Builder $builder) use ($status): void {
                $statusValue = match ($status) {
                    'pending' => ComplaintStatus::PENDING,
                    'analysis' => ComplaintStatus::UNDER_REVIEW,
                    'in_progress' => ComplaintStatus::IN_PROGRESS,
                    'resolved' => ComplaintStatus::RESOLVED,
                };

                $builder->where('status', $statusValue);
            });
    }

    private function dashboardTrendPeriod(Request $request): string
    {
        return 'month';
    }

    /** @return array<string, mixed> */
    private function dashboardFilters(Request $request): array
    {
        $allDates = $request->boolean('all_dates');

        return [
            'period' => 'month',
            'start_date' => $allDates ? null : ($request->input('start_date') ?: now()->startOfYear()->toDateString()),
            'end_date' => $allDates ? null : ($request->input('end_date') ?: now()->toDateString()),
            ...$request->only(['category', 'status']),
            ...($allDates ? ['all_dates' => true] : []),
        ];
    }

    private function trendStatusCounts(Collection $complaints, string $period): Collection
    {
        return $complaints
            ->filter(fn (array $complaint): bool => filled($complaint['createdAt']))
            ->groupBy(function (array $complaint) use ($period): string {
                $date = \Illuminate\Support\Carbon::parse($complaint['createdAt']);

                return match ($period) {
                    'week' => $date->startOfWeek()->format('Y-m-d'),
                    'month' => $date->startOfMonth()->format('Y-m'),
                    'year' => $date->startOfYear()->format('Y'),
                    default => $date->format('Y-m-d'),
                };
            })
            ->sortKeys()
            ->map(fn (Collection $periodComplaints): array => [
                'urgent' => $periodComplaints->where('highPriority', true)->count(),
                'pending' => $periodComplaints->where('_status', ComplaintStatus::PENDING)->count(),
                'analysis' => $periodComplaints->where('_status', ComplaintStatus::UNDER_REVIEW)->count(),
                'in_progress' => $periodComplaints->where('_status', ComplaintStatus::IN_PROGRESS)->count(),
                'resolved' => $periodComplaints->where('_status', ComplaintStatus::RESOLVED)->count(),
            ]);
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
            'justification' => $this->latestStatusJustification($complaint),
            'date' => $complaint->created_at?->format('d/m/Y H:i'),
        ])->values();
    }

    private function latestStatusJustification(Complaint $complaint): ?string
    {
        return DB::table('complaint_status_histories')
            ->where('complaint_id', $complaint->complaint_id)
            ->whereIn('to_status', [ComplaintStatus::RESOLVED->value, ComplaintStatus::REJECTED->value])
            ->whereNotNull('justification')
            ->latest('id')
            ->value('justification');
    }
}
