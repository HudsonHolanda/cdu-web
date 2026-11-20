import './masks';
import './nav';
import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

window.DataTable = DataTable;
window.L = L;

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
        in_progress: 'Denúncias em andamento',
        normal: 'Denúncias normais',
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
                <td class="px-3 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium ${complaint.filter === 'resolved' ? 'bg-emerald-100 text-emerald-700' : complaint.filter === 'analysis' ? 'bg-sky-100 text-sky-700' : complaint.filter === 'in_progress' ? 'bg-blue-100 text-blue-700' : complaint.filter === 'urgent' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700'}">${escapeHtml(complaint.status)}</span></td>
            </tr>
        `).join('');
        empty.classList.toggle('hidden', filtered.length > 0);
        document.querySelectorAll('[data-overview-filter]').forEach((button) => {
            button.setAttribute('aria-expanded', String(button.dataset.overviewFilter === filter));
        });
        window.dispatchEvent(new CustomEvent('overview-map-filter', { detail: { filter } }));
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
        in_progress: 'Denúncias em andamento',
        resolved: 'Denúncias resolvidas',
        urgent: 'Denúncias urgentes',
        normal: 'Denúncias normais',
    };
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
    })[character]);

    const matchesFilter = (complaint, filter) => {
        if (filter === 'all') return true;
        if (filter === 'urgent') return Boolean(complaint.highPriority) || complaint.priority === 'Urgente';

        const status = String(complaint.status || '').toLowerCase();
        if (filter === 'analysis') return status.includes('anál') || status.includes('anali');
        if (filter === 'in_progress') return status.includes('andamento');
        if (filter === 'normal') return !Boolean(complaint.highPriority) && complaint.priority !== 'Urgente';
        if (filter === 'resolved') return status.includes('resolv') || status.includes('conclu');
        return status.includes('pendent');
    };

    const statusClass = (status) => {
        const normalized = String(status || '').toLowerCase();
        if (normalized.includes('resolv') || normalized.includes('conclu')) return 'bg-emerald-100 text-emerald-700';
        if (normalized.includes('anál') || normalized.includes('anali')) return 'bg-sky-100 text-sky-700';
        if (normalized.includes('andamento')) return 'bg-blue-100 text-blue-700';
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
                <td class="px-3 py-3"><a href="/denuncias/detalhe/${encodeURIComponent(complaint.publicId || '')}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Ver</a></td>
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

const setupAgentDataTables = () => {
    const tables = document.querySelectorAll('[data-agent-datatable]');
    if (!tables.length) return;

    if (typeof window.DataTable === 'undefined') {
        window.setTimeout(setupAgentDataTables, 100);
        return;
    }

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
    })[character]);
    const statusClass = (status) => {
        const normalized = String(status || '').toLowerCase();
        if (normalized.includes('resolv') || normalized.includes('conclu')) return 'bg-emerald-100 text-emerald-700';
        if (normalized.includes('anál') || normalized.includes('anali')) return 'bg-sky-100 text-sky-700';
        if (normalized.includes('andamento')) return 'bg-blue-100 text-blue-700';
        if (normalized.includes('pendent')) return 'bg-amber-100 text-amber-700';
        return 'bg-rose-100 text-rose-700';
    };

    const refreshDashboard = async (url, trigger = null) => {
        if (trigger) {
            trigger.setAttribute('aria-busy', 'true');
            trigger.classList.add('opacity-60', 'pointer-events-none');
        }

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
            });
            if (!response.ok) throw new Error(`Dashboard request failed: ${response.status}`);

            const html = await response.text();
            const documentParser = new DOMParser();
            const nextDocument = documentParser.parseFromString(html, 'text/html');
            const currentPage = document.querySelector('.cdu-agent-page');
            const nextPage = nextDocument.querySelector('.cdu-agent-page');

            if (!currentPage || !nextPage) throw new Error('Dashboard content not found');

            currentPage.replaceWith(nextPage);
            window.history.pushState({}, '', url.toString());
            setupAgentDataTables();
        } catch (error) {
            window.location.assign(url.toString());
        }
    };

    tables.forEach((tableElement) => {
        if (tableElement.dataset.dataTableReady === 'true') return;
        tableElement.dataset.dataTableReady = 'true';
        const context = tableElement.dataset.agentDatatable;

        const dataTable = new window.DataTable(tableElement, {
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthMenu: [[10, 20, 30, 50, 100], [10, 20, 30, 50, 100]],
            searchDelay: 350,
            order: [[3, 'desc']],
            ajax: {
                url: tableElement.dataset.datatableUrl,
                data: (params) => {
                    params.context = context;
                    params.status = tableElement.dataset.status || '';
                    if (context === 'dashboard') {
                        params.start_date = tableElement.dataset.startDate || '';
                        params.end_date = tableElement.dataset.endDate || '';
                        params.category = tableElement.dataset.category || '';
                        params.status = tableElement.dataset.status || '';
                    }
                },
            },
            language: {
                processing: 'Carregando...',
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ denúncias por página',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ denúncias',
                infoEmpty: 'Nenhuma denúncia encontrada',
                infoFiltered: '(filtrado de _MAX_ denúncias)',
                zeroRecords: 'Nenhuma denúncia encontrada',
                paginate: { first: 'Primeira', last: 'Última', next: 'Próxima', previous: 'Anterior' },
            },
            columns: [
                { data: 'id', className: 'px-3 py-3 font-medium text-slate-500' },
                { data: 'title', className: 'px-3 py-3' },
                { data: 'district', className: 'px-3 py-3' },
                { data: 'date', className: 'px-3 py-3' },
                {
                    data: 'priority',
                    className: 'px-3 py-3',
                    render: (value) => `<span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">${escapeHtml(value)}</span>`,
                },
                {
                    data: 'status',
                    className: 'px-3 py-3',
                    render: (value) => `<span class="inline-flex rounded-full px-2 py-1 text-xs font-medium ${statusClass(value)}">${escapeHtml(value)}</span>`,
                },
                {
                    data: 'publicId',
                    orderable: false,
                    searchable: false,
                    className: 'px-3 py-3',
                    render: (value) => `<a href="/denuncias/detalhe/${encodeURIComponent(value || '')}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Ver</a>`,
                },
            ],
        });

        const updateActiveFilterCards = (filter) => {
            document.querySelectorAll('[data-filter-card]').forEach((card) => {
                card.classList.toggle('is-active', Boolean(filter) && card.dataset.filterCard === filter);
            });
        };

        document.querySelectorAll('[data-agent-show-all]').forEach((button) => {
            if (button.dataset.dataTableBound === 'true') return;
            button.dataset.dataTableBound = 'true';
            button.addEventListener('click', () => {
                if (context === 'dashboard') {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('status');
                    refreshDashboard(url, button);
                    return;
                }

                tableElement.dataset.status = '';
                updateActiveFilterCards('');
                document.querySelector('[data-overview-filter-title]')?.replaceChildren(document.createTextNode('Ocorrências Recentes'));
                document.querySelectorAll('[data-overview-filter]').forEach((item) => item.setAttribute('aria-expanded', 'false'));
                window.dispatchEvent(new CustomEvent('overview-map-filter', { detail: { filter: 'all' } }));
                dataTable.page.len(100).page(0).draw();
            });
        });

        if (context === 'overview') {
            document.querySelectorAll('[data-overview-filter]').forEach((button) => {
                if (button.dataset.dataTableBound === 'true') return;
                button.dataset.dataTableBound = 'true';
                button.addEventListener('click', () => {
                    const requestedFilter = button.dataset.overviewFilter || 'all';
                    const currentFilter = tableElement.dataset.status || '';
                    const filter = currentFilter === requestedFilter ? 'all' : requestedFilter;
                    tableElement.dataset.status = filter === 'all' ? '' : filter;
                    dataTable.ajax.reload();
                    updateActiveFilterCards(tableElement.dataset.status);
                    document.querySelectorAll('[data-overview-filter]').forEach((item) => item.setAttribute('aria-expanded', String(filter !== 'all' && item === button)));
                    window.dispatchEvent(new CustomEvent('overview-map-filter', { detail: { filter } }));
                });
            });
        }

        if (context === 'dashboard') {
            document.querySelectorAll('[data-dashboard-filter]').forEach((button) => {
                if (button.dataset.dataTableBound === 'true') return;
                button.dataset.dataTableBound = 'true';
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const requestedFilter = button.dataset.dashboardFilter || '';
                    const currentFilter = tableElement.dataset.status || '';
                    const nextFilter = currentFilter === requestedFilter ? '' : requestedFilter;
                    const url = new URL(button.href || window.location.href, window.location.origin);

                    if (nextFilter) {
                        url.searchParams.set('status', nextFilter);
                    } else {
                        url.searchParams.delete('status');
                    }

                    refreshDashboard(url, button);
                });
            });
        }
    });
};

setTheme(localStorage.getItem(themeKey) || 'light');
setupBackToTop();
setupBackofficeSidebar();
setupOverviewFilters();
setupDashboardTable();
setupAgentDataTables();
document.addEventListener('livewire:navigated', () => {
    setTheme(localStorage.getItem(themeKey) || 'light');
    setupBackToTop();
    setupBackofficeSidebar();
    setupOverviewFilters();
    setupDashboardTable();
    setupAgentDataTables();
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
