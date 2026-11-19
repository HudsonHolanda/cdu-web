<div>
    <button type="button" class="cdu-sidebar-toggle" data-sidebar-toggle aria-expanded="false" aria-controls="cdu-sidebar">
        <span class="sr-only">Abrir menu</span>
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
    </button>
    <div class="cdu-sidebar-overlay" data-sidebar-overlay></div>
<aside id="cdu-sidebar" class="cdu-sidebar" data-sidebar aria-label="Navegação administrativa">
    <a href="{{ route('dashboard') }}" class="cdu-sidebar-brand" wire:navigate>
        <img src="{{ Vite::asset('resources/images/logo_invert.svg') }}" alt="C.D.U">
        <span>CDU</span>
    </a>
    <nav class="cdu-sidebar-nav">
        <a href="{{ route('agent.overview') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('agent.overview') ? 'is-active' : '' }}" wire:navigate><span><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 11.5 12 4l9 7.5M5 10v9h14v-9M9 19v-5h6v5" /></svg></span> Visão Geral</a>
        <a href="{{ route('agent.dashboard') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('agent.dashboard') ? 'is-active' : '' }}" wire:navigate><span><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg></span> Dashboard</a>
        <a href="{{ route('complaints.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('complaints.index', 'complaints.create', 'complaints.store', 'complaints.show') ? 'is-active' : '' }}" wire:navigate><span><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h4" /></svg></span> Denúncias</a>
        @agent
            <a href="{{ route('agents.index') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('agents.*') ? 'is-active' : '' }}" wire:navigate><span><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="8" r="3"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 20c.8-3.2 3.1-5 7-5s6.2 1.8 7 5" /></svg></span> Usuários</a>
            <a href="{{ route('agent.mapa') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('agent.mapa') ? 'is-active' : '' }}" wire:navigate><span><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s6-5.2 6-11a6 6 0 1 0-12 0c0 5.8 6 11 6 11Z"/><circle cx="12" cy="10" r="2"/></svg></span> Mapa</a>
            <a href="{{ route('agent.relatorios') }}" @click="sidebarOpen = false" class="{{ request()->routeIs('agent.relatorios') ? 'is-active' : '' }}" wire:navigate><span><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 4h12v16H6zM9 8h6M9 12h6M9 16h3" /></svg></span> Relatórios</a>
        @endagent
    </nav>
    <div class="cdu-sidebar-bottom">
        <x-theme-toggle />
        <a href="{{ route('profile') }}" @click="sidebarOpen = false" wire:navigate><span><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 1.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.1h-2.5V20a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1-1.8-1.8.1-.1A1.7 1.7 0 0 0 8.1 15a1.7 1.7 0 0 0-1.6-1H6v-2.5h.5a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 1.8-1.8.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V5H15v.5a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1 1.8 1.8-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1V14h-.1a1.7 1.7 0 0 0-1.6 1Z" /></svg></span> Configurações</a>
        <form method="POST" action="{{ route('logout') }}" x-data>
            @csrf
            <button type="submit"><span>↪</span> Sair</button>
        </form>
    </div>
</aside>
</div>
