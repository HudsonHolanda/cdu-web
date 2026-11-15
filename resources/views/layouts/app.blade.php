<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#f5f7f5">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'C.D.U') }}</title>

        <script>document.documentElement.classList.toggle('dark', localStorage.getItem('cdu-theme') === 'dark');</script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen min-h-dvh cdu-app-shell pb-[env(safe-area-inset-bottom)]">
            @if (auth()->user()?->hasRole(\App\Enums\UserRole::AGENT))
                <x-backoffice-sidebar />
            @else
                <livewire:layout.navigation />
            @endif

            <!-- Page Heading -->
            @if (isset($header) && ! auth()->user()?->hasRole(\App\Enums\UserRole::AGENT))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="{{ auth()->user()?->hasRole(\App\Enums\UserRole::AGENT) ? 'cdu-backoffice-main' : '' }}">
                @if (auth()->user()?->hasRole(\App\Enums\UserRole::AGENT))
                    <div class="cdu-backoffice-topbar">
                        <div class="cdu-search">⌕ <span>Buscar</span></div>
                        <div class="cdu-user-chip"><span class="cdu-user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->isAdmin() ? 'Administrador' : 'Agente' }}</small></span></div>
                    </div>
                    @if (isset($header))
                        <div class="cdu-backoffice-heading">{{ $header }}</div>
                    @endif
                @endif
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
