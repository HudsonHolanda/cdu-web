import './masks';
import './nav';

const themeKey = 'cdu-theme';
const setTheme = (theme) => {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    document.documentElement.dataset.theme = theme;
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(theme === 'dark'));
        button.setAttribute('title', theme === 'dark' ? 'Usar tema claro' : 'Usar tema escuro');
        const label = button.querySelector('[data-theme-label]');
        if (label) label.textContent = theme === 'dark' ? 'Tema claro' : 'Tema escuro';
    });
};

const setupBackToTop = () => {
    const button = document.querySelector('[data-back-to-top]');
    if (!button || button.dataset.ready) return;

    button.dataset.ready = 'true';
    const updateVisibility = () => button.classList.toggle('is-visible', window.scrollY > 420);
    window.addEventListener('scroll', updateVisibility, { passive: true });
    button.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    updateVisibility();
};

const setupBackofficeSidebar = () => {
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.querySelector('[data-sidebar]');
    const overlay = document.querySelector('[data-sidebar-overlay]');

    if (!toggle || !sidebar || !overlay || toggle.dataset.ready) return;

    toggle.dataset.ready = 'true';

    const setOpen = (open) => {
        sidebar.classList.toggle('is-open', open);
        overlay.classList.toggle('is-visible', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.querySelector('.sr-only').textContent = open ? 'Fechar menu' : 'Abrir menu';
    };

    toggle.addEventListener('click', () => setOpen(!sidebar.classList.contains('is-open')));
    overlay.addEventListener('click', () => setOpen(false));
    sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setOpen(false);
    });
    window.matchMedia('(min-width: 851px)').addEventListener('change', (event) => {
        if (event.matches) setOpen(false);
    });
};

const setupOverviewFilters = () => {
    const dataElement = document.querySelector('#overview-complaints-data');
    const panel = document.querySelector('[data-overview-results-panel]');
    const rows = document.querySelector('[data-overview-filter-rows]');
    const empty = document.querySelector('[data-overview-filter-empty]');
    const title = document.querySelector('[data-overview-filter-title]');

    if (!dataElement || !panel || !rows || dataElement.dataset.ready) return;

    dataElement.dataset.ready = 'true';

    const complaints = JSON.parse(dataElement.textContent || '[]');
    const labels = {
        all: 'Todas as denúncias',
        pending: 'Denúncias pendentes',
        analysis: 'Denúncias em análise',
        resolved: 'Denúncias resolvidas',
        urgent: 'Denúncias urgentes',
    };
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
    })[character]);

    const render = (filter) => {
        const filtered = filter === 'all' ? complaints : complaints.filter((complaint) => complaint.filters?.includes(filter));
        title.textContent = labels[filter] || labels.all;
        rows.innerHTML = filtered.map((complaint) => `
            <tr class="border-b border-slate-100 last:border-b-0">
                <td class="px-3 py-3 font-medium text-slate-500">${escapeHtml(complaint.id)}</td>
                <td class="px-3 py-3">${escapeHtml(complaint.occurrence)}</td>
                <td class="px-3 py-3">${escapeHtml(complaint.location)}</td>
                <td class="px-3 py-3">${escapeHtml(complaint.date)}</td>
                <td class="px-3 py-3"><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">${escapeHtml(complaint.priority)}</span></td>
                <td class="px-3 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ${complaint.filter === 'resolved' ? 'bg-emerald-100 text-emerald-700' : complaint.filter === 'analysis' ? 'bg-sky-100 text-sky-700' : complaint.filter === 'urgent' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'}">${escapeHtml(complaint.status)}</span></td>
            </tr>
        `).join('');
        empty.classList.toggle('hidden', filtered.length > 0);
        document.querySelectorAll('[data-overview-filter]').forEach((button) => {
            button.setAttribute('aria-expanded', String(button.dataset.overviewFilter === filter));
        });
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    document.querySelectorAll('[data-overview-filter]').forEach((button) => {
        button.addEventListener('click', () => render(button.dataset.overviewFilter || 'all'));
    });

};

const setupDashboardTable = () => {
    const dataElement = document.querySelector('#dashboard-complaints-data');
    const buttons = document.querySelectorAll('[data-dashboard-filter]');
    const rows = document.querySelector('[data-dashboard-complaint-rows]');
    const empty = document.querySelector('[data-dashboard-empty]');
    const title = document.querySelector('[data-dashboard-filter-title]');

    if (!dataElement || !buttons.length || !rows || !title || dataElement.dataset.ready) return;

    dataElement.dataset.ready = 'true';
    const complaints = JSON.parse(dataElement.textContent || '[]');
    const labels = {
        all: 'Todas as denúncias',
        pending: 'Denúncias pendentes',
        analysis: 'Denúncias em análise',
        resolved: 'Denúncias resolvidas',
        urgent: 'Denúncias urgentes',
    };
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
    })[character]);

    const matchesFilter = (complaint, filter) => {
        if (filter === 'all') return true;
        if (filter === 'urgent') return Boolean(complaint.highPriority) || complaint.priority === 'Urgente';

        const status = String(complaint.status || '').toLowerCase();
        if (filter === 'analysis') return status.includes('anál') || status.includes('anali');
        if (filter === 'resolved') return status.includes('resolv') || status.includes('conclu');
        return status.includes('pendent');
    };

    const statusClass = (status) => {
        const normalized = String(status || '').toLowerCase();
        if (normalized.includes('resolv') || normalized.includes('conclu')) return 'bg-emerald-100 text-emerald-700';
        if (normalized.includes('anál') || normalized.includes('anali')) return 'bg-sky-100 text-sky-700';
        if (normalized.includes('pendent')) return 'bg-amber-100 text-amber-700';
        return 'bg-rose-100 text-rose-700';
    };

    const render = (filter) => {
        const filtered = complaints.filter((complaint) => matchesFilter(complaint, filter));
        rows.innerHTML = filtered.map((complaint) => `
            <tr class="border-b border-slate-100 last:border-b-0">
                <td class="px-3 py-3 font-medium text-slate-500">${escapeHtml(complaint.id)}</td>
                <td class="px-3 py-3">${escapeHtml(complaint.title)}</td>
                <td class="px-3 py-3">${escapeHtml(complaint.location?.district || 'Não informado')}</td>
                <td class="px-3 py-3">${escapeHtml(complaint.date)}</td>
                <td class="px-3 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">${escapeHtml(complaint.priority)}</span></td>
                <td class="px-3 py-3"><span class="rounded-full px-2 py-1 text-xs font-medium ${statusClass(complaint.status)}">${escapeHtml(complaint.status)}</span></td>
                <td class="px-3 py-3"><button type="button" class="text-sm font-medium text-slate-700 hover:text-slate-900">Ver</button></td>
            </tr>
        `).join('');
        title.textContent = labels[filter] || labels.all;
        empty.classList.toggle('hidden', filtered.length > 0);
        buttons.forEach((button) => {
            button.setAttribute('aria-expanded', String(button.dataset.dashboardFilter === filter));
        });
    };

    buttons.forEach((button) => {
        if (button.tagName === 'A' && button.getAttribute('href')) return;
        button.addEventListener('click', () => render(button.dataset.dashboardFilter || 'all'));
    });
};

setTheme(localStorage.getItem(themeKey) || 'light');
setupBackToTop();
setupBackofficeSidebar();
setupOverviewFilters();
setupDashboardTable();
document.addEventListener('livewire:navigated', () => {
    setTheme(localStorage.getItem(themeKey) || 'light');
    setupBackToTop();
    setupBackofficeSidebar();
    setupOverviewFilters();
    setupDashboardTable();
});

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');
    if (!button) return;
    const theme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
    localStorage.setItem(themeKey, theme);
    setTheme(theme);
});

const refreshProfileAvatars = (event) => {
    const version = event.detail?.avatarVersion || Date.now();

    document.querySelectorAll('[data-profile-avatar]').forEach((avatar) => {
        const imageUrl = avatar.dataset.imageUrl;
        if (!imageUrl) return;

        if (avatar.tagName === 'IMG') {
            avatar.src = `${imageUrl}?v=${version}`;
            return;
        }

        const image = document.createElement('img');
        image.src = `${imageUrl}?v=${version}`;
        image.alt = 'Foto de perfil';
        image.className = avatar.dataset.avatarClass || avatar.className;
        image.dataset.profileAvatar = '';
        image.dataset.imageUrl = imageUrl;
        avatar.replaceWith(image);
    });
};

window.addEventListener('profile-updated', refreshProfileAvatars);
