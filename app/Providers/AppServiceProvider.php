<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
          // Só o admin cadastra, edita e remove agentes
        Gate::define('manage-agents', fn (User $user) => $user->isAdmin()); 
    }
}
