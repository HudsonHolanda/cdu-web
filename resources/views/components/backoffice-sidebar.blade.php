<aside class="cdu-sidebar" aria-label="Navegação administrativa">
    <a href="{{ route('dashboard') }}" class="cdu-sidebar-brand" wire:navigate>
        <img src="{{ Vite::asset('resources/images/logo_invert.svg') }}" alt="C.D.U">
        <span>CDU</span>
    </a>
    <nav class="cdu-sidebar-nav">
        <a href="{{ route('agent.overview') }}" class="{{ request()->routeIs('agent.overview') ? 'is-active' : '' }}" wire:navigate><span>⌂</span> Visão Geral</a>
        <a href="{{ route('agent.dashboard') }}" class="{{ request()->routeIs('agent.dashboard') ? 'is-active' : '' }}" wire:navigate><span>▦</span> Dashboard</a>
        <a href="{{ route('complaints.index') }}" class="{{ request()->routeIs('complaints.index', 'complaints.create', 'complaints.store', 'complaints.show') ? 'is-active' : '' }}" wire:navigate><span>▤</span> Denúncias</a>
        <a href="{{ route('complaints.mine') }}" class="{{ request()->routeIs('complaints.mine', 'complaints.edit', 'complaints.update', 'complaints.destroy') ? 'is-active' : '' }}" wire:navigate><span>▣</span> Minhas denúncias</a>
        @agent
            <a href="{{ route('agents.index') }}" class="{{ request()->routeIs('agents.*') ? 'is-active' : '' }}" wire:navigate><span>♙</span> Usuários</a>
            <a href="{{ route('agent.mapa') }}" class="{{ request()->routeIs('agent.mapa') ? 'is-active' : '' }}" wire:navigate><span>⌖</span> Mapa</a>
            <a href="{{ route('agent.relatorios') }}" class="{{ request()->routeIs('agent.relatorios') ? 'is-active' : '' }}" wire:navigate><span>▥</span> Relatórios</a>
        @endagent
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
