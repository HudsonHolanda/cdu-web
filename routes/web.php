<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\BackofficeController;

Route::view('/', 'home');

Route::get('dashboard', function (\Illuminate\Http\Request $request, BackofficeController $backoffice) {
    return $request->user()->hasRole(\App\Enums\UserRole::AGENT)
        ? $backoffice->home()
        : view('dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('dashboard/analytics', [BackofficeController::class, 'dashboard'])
    ->middleware(['auth', 'verified', 'role:agent'])
    ->name('admin.dashboard');

Route::middleware(['auth'])->prefix('denuncias')->name('complaints.')->group(function () {
    Route::get('/', [ComplaintController::class, 'index'])->name('index');
    Route::get('/minhas', [ComplaintController::class, 'mine'])->name('mine');
    Route::get('/nova', [ComplaintController::class, 'create'])->name('create');
    Route::post('/', [ComplaintController::class, 'store'])->name('store');
    Route::get('/detalhe/{publicId}', [ComplaintController::class, 'show'])->name('show');
    Route::get('/imagens/{filename}', [ComplaintController::class, 'image'])->name('image');
    Route::get('/{complaint}/editar', [ComplaintController::class, 'edit'])->name('edit');
    Route::put('/{complaint}', [ComplaintController::class, 'update'])->name('update');
    Route::patch('/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('status');
    Route::delete('/{complaint}', [ComplaintController::class, 'destroy'])->name('destroy');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');



    // Gestão de agentes: apenas administradores (Gate "manage-agents")
Route::middleware(['auth', 'verified', 'can:manage-agents'])
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
