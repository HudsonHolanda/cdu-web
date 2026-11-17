<?php

namespace App\Http\Controllers;

use App\Services\ComplaintRepository;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class BackofficeController extends Controller
{
    public function home(ComplaintRepository $repository): View
    {
        $complaints = $repository->all();

        return view('backoffice.home', $this->metrics($complaints));
    }

    public function dashboard(ComplaintRepository $repository): View
    {
        $complaints = $repository->all();

        return view('backoffice.dashboard', $this->metrics($complaints));
    }

    /** @return array<string, mixed> */
    private function metrics(Collection $complaints): array
    {
        $status = fn (string $value): int => $complaints->filter(fn (array $item) => mb_strtolower($item['status'] ?? '') === mb_strtolower($value))->count();

        return [
            'complaints' => $complaints->sortByDesc('createdAt')->values(),
            'total' => $complaints->count(),
            'pending' => $status('Em aberto'),
            'analysis' => $status('Em análise'),
            'resolved' => $status('Resolvida') + $status('Concluída'),
            'urgent' => $complaints->where('highPriority', true)->count(),
            'categories' => $complaints->groupBy('category')->map->count()->sortDesc(),
        ];
    }
}
