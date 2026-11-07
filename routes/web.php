<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;

Route::view('/', 'home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
