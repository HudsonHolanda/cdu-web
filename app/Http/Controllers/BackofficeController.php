<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BackofficeController extends Controller
{
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
                'complaintId' => 'DEN-'.now()->format('Y').'-'.$complaint->complaint_id,
                'category' => $complaint->category,
                'title' => $complaint->title,
                'status' => $complaint->status?->label() ?? 'Pendente',
                'highPriority' => (bool) $complaint->high_priority,
                'createdAt' => $complaint->created_at?->toIso8601String(),
                'location' => ['district' => $complaint->district],
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
}
