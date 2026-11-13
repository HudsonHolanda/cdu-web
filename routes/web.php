<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ComplaintController;

Route::view('/', 'home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('denuncias')->name('complaints.')->group(function () {
    Route::get('/', [ComplaintController::class, 'index'])->name('index');
    Route::get('/nova', [ComplaintController::class, 'create'])->name('create');
    Route::post('/', [ComplaintController::class, 'store'])->name('store');
    Route::get('/detalhe/{publicId}', [ComplaintController::class, 'show'])->name('show');
    Route::get('/imagens/{filename}', [ComplaintController::class, 'image'])->name('image');
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
