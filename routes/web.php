<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\BackofficeController;
use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    /** @var \Illuminate\Database\Eloquent\Collection<int, Complaint> $complaints */
    $complaints = Complaint::query()
        ->get(['latitude', 'longitude', 'district', 'status', 'high_priority']);
    $total = $complaints->count();
    $resolved = $complaints->where('status', ComplaintStatus::RESOLVED)->count();
    $districts = $complaints
        ->pluck('district')
        ->map(fn (?string $district): string => trim((string) $district))
        ->filter()
        ->unique()
        ->count();

    $mapMarkers = $complaints->map(fn (Complaint $complaint): array => [
        'coords' => [(float) $complaint->latitude, (float) $complaint->longitude],
        'district' => trim((string) $complaint->district) ?: 'Bairro não informado',
        'status' => $complaint->status?->label() ?? 'Pendente',
        'color' => $complaint->high_priority ? '#ef4444' : match ($complaint->status) {
            ComplaintStatus::RESOLVED => '#22c55e',
            ComplaintStatus::UNDER_REVIEW, ComplaintStatus::IN_PROGRESS => '#0ea5e9',
            default => '#f59e0b',
        },
    ])->values();

    return view('home', [
        'homeStats' => [
            'total' => $total,
            'resolvedPercent' => $total > 0 ? (int) round(($resolved / $total) * 100) : 0,
            'districts' => $districts,
        ],
        'homeMapMarkers' => $mapMarkers,
    ]);
});

Route::get('dashboard', function (\Illuminate\Http\Request $request, BackofficeController $backoffice) {
    return $request->user()->hasRole(\App\Enums\UserRole::AGENT)
        ? $backoffice->agentOverview($request)
        : view('dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('dashboard/analytics', [BackofficeController::class, 'agentDashboard'])
    ->middleware(['auth', 'verified', 'role:agent'])
    ->name('admin.dashboard');
    
Route::middleware(['auth', 'verified', 'can:access-agent-panel'])->group(function () {
    Route::get('painel', [BackofficeController::class, 'agentOverview'])->name('agent.overview');
    Route::get('painel/dashboard', [BackofficeController::class, 'agentDashboard'])->name('agent.dashboard');
    Route::get('painel/denuncias/tabela', [BackofficeController::class, 'agentComplaintsTable'])->name('agent.complaints.table');
    Route::get('painel/dashboard/pdf', [BackofficeController::class, 'agentDashboardPdf'])->name('agent.dashboard.pdf');
    Route::get('painel/mapa', [BackofficeController::class, 'agentMap'])->name('agent.mapa');
    Route::get('painel/relatorios', [BackofficeController::class, 'agentReports'])->name('agent.relatorios');
    Route::get('painel/relatorios/tabela', [BackofficeController::class, 'agentReportsTable'])->name('agent.relatorios.table');
    Route::get('painel/relatorios/pdf', [BackofficeController::class, 'agentReportPdf'])->name('agent.relatorios.pdf');
});

Route::middleware(['auth'])->prefix('denuncias')->name('complaints.')->group(function () {
    Route::get('/', [ComplaintController::class, 'index'])->name('index');
    Route::get('/minhas', [ComplaintController::class, 'mine'])->name('mine');
    Route::get('/nova', [ComplaintController::class, 'create'])->name('create');
    Route::post('/', [ComplaintController::class, 'store'])->name('store');
    Route::get('/detalhe/{publicId}', [ComplaintController::class, 'show'])->name('show');
    Route::get('/imagens/{filename}', [ComplaintController::class, 'image'])->name('image');
    Route::get('/solicitacoes-exclusao', [ComplaintController::class, 'deletionRequests'])->name('deletion-requests');
    Route::patch('/solicitacoes-exclusao/{deletionRequest}', [ComplaintController::class, 'reviewDeletionRequest'])->name('deletion-requests.review');
    Route::get('/{complaint}/solicitar-exclusao', [ComplaintController::class, 'createDeletionRequest'])->name('deletion-requests.create');
    Route::post('/{complaint}/solicitar-exclusao', [ComplaintController::class, 'storeDeletionRequest'])->name('deletion-requests.store');
    Route::patch('/solicitacoes-alteracao/{updateRequest}', [ComplaintController::class, 'reviewUpdateRequest'])->name('update-requests.review');
    Route::get('/{complaint}/solicitar-alteracao', [ComplaintController::class, 'createUpdateRequest'])->name('update-requests.create');
    Route::post('/{complaint}/solicitar-alteracao', [ComplaintController::class, 'storeUpdateRequest'])->name('update-requests.store');
    Route::get('/{complaint}/editar', [ComplaintController::class, 'edit'])->name('edit');
    Route::put('/{complaint}', [ComplaintController::class, 'update'])->name('update');
    Route::patch('/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('status');
    Route::delete('/{complaint}', [ComplaintController::class, 'destroy'])->name('destroy');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('profile/image', function (\Illuminate\Http\Request $request) {
    $path = $request->user()?->profile_image;
    abort_unless($path, 404);

    $profileDisk = config('filesystems.profile');
    if (Storage::disk($profileDisk)->exists($path)) {
        return Storage::disk($profileDisk)->response($path, null, [
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }

    // Backward compatibility for profile images saved before the disk became configurable.
    abort_unless($profileDisk !== 'public' && Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path, null, [
        'Cache-Control' => 'private, max-age=86400',
    ]);
})->middleware(['auth'])->name('profile.image');

Route::get('denuncias/geocode', [ComplaintController::class, 'geocode'])
    ->middleware(['auth'])
    ->name('complaints.geocode');



    // Gestão de agentes: apenas administradores (Gate "manage-agents")
Route::middleware(['auth', 'verified', 'can:access-agents'])
    ->prefix('agents')
    ->name('agents.')
    ->group(function () {
        Route::get('/', [AgentController::class, 'index'])->name('index');
        Route::post('/', [AgentController::class, 'store'])->name('store');
        Route::get('{agent}/edit', [AgentController::class, 'edit'])->name('edit');
        Route::put('{agent}', [AgentController::class, 'update'])->name('update');
        Route::delete('{agent}', [AgentController::class, 'destroy'])->name('destroy');
    });
require __DIR__.'/auth.php';
