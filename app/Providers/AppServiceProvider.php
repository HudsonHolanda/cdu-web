<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Blade;
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
        // Define gates para as user roles
        Gate::define('report-problems',    fn (User $u) => $u->hasRole(UserRole::CITIZEN));
        Gate::define('manage-resolutions', fn (User $u) => $u->hasRole(UserRole::AGENT));
        Gate::define('manage-agents',      fn (User $u) => $u->hasRole(UserRole::ADMIN));
        Gate::define('moderate',           fn (User $u) => $u->hasRole(UserRole::ADMIN));

        // Diretivas pra cada UserRole, para uso nos templates Blade
        Blade::if('citizen', fn () => auth()->user()?->hasRole(UserRole::CITIZEN));
        Blade::if('agent', fn () => auth()->user()?->hasRole(UserRole::AGENT));
        Blade::if('admin', fn () => auth()->user()?->hasRole(UserRole::ADMIN));
    }
}
