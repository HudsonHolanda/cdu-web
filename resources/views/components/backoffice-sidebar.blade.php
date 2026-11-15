<aside class="cdu-sidebar" aria-label="Navegação administrativa">
    <a href="{{ route('dashboard') }}" class="cdu-sidebar-brand" wire:navigate>
        <img src="{{ Vite::asset('resources/images/logo_invert.svg') }}" alt="C.D.U">
        <span>CDU</span>
    </a>
    <nav class="cdu-sidebar-nav">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}" wire:navigate><span>⌂</span> Visão Geral</a>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" wire:navigate><span>▦</span> Dashboard</a>
        <a href="{{ route('complaints.index') }}" class="{{ request()->routeIs('complaints.*') ? 'is-active' : '' }}" wire:navigate><span>▤</span> Denúncias</a>
        @admin
            <a href="{{ route('agents.index') }}" class="{{ request()->routeIs('agents.*') ? 'is-active' : '' }}" wire:navigate><span>♙</span> Usuários</a>
            <a href="#" class="cdu-sidebar-disabled"><span>▥</span> Relatórios</a>
        @endadmin
    </nav>
    <div class="cdu-sidebar-bottom">
        <x-theme-toggle />
        <a href="{{ route('profile') }}" wire:navigate><span>⚙</span> Configurações</a>
        <form method="POST" action="{{ route('logout') }}" x-data>
            @csrf
            <button type="submit"><span>↪</span> Sair</button>
        </form>
    </div>
</aside>
