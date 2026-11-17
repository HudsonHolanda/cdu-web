<x-app-layout>
    @php
        $user = auth()->user();
        $roleLabel = $user?->isAdmin() ? 'Admin' : ($user?->isAgent() ? 'Agente' : 'Usuário');
    @endphp

    <div class="min-h-screen bg-[#efefee] text-slate-800">
        <div class="flex min-h-screen">
            <aside class="hidden w-64 flex-col justify-between bg-[#1d3b34] px-5 py-6 text-white lg:flex">
                <div>
                    <div class="mb-10 flex items-center gap-3 px-3">
                        <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl bg-white/10 shadow-sm ring-1 ring-white/20">
                            <img src="{{ Vite::asset('resources/images/logo.svg') }}" alt="CDU" class="h-9 w-9 object-contain" style="filter: brightness(0) invert(1);">
                        </div>
                        <div class="text-2xl font-black tracking-tight text-white">CDU</div>
                    </div>

                    <nav class="space-y-2">
                        <a href="{{ route('agent.overview') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">◫</span>
                            Visão Geral
                        </a>
                        <a href="{{ route('agent.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">▣</span>
                            Dashboard
                        </a>
                        <a href="{{ route('agent.mapa') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">▤</span>
                            Mapa
                        </a>
                        <a href="{{ route('agents.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">◎</span>
                            Usuários
                        </a>
                        <a href="{{ route('agent.relatorios') }}" class="flex items-center gap-3 rounded-xl bg-white/10 px-3 py-3 text-sm font-medium text-white ring-1 ring-white/10">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/10">◌</span>
                            Relatórios
                        </a>
                    </nav>
                </div>

                <div class="space-y-2 border-t border-white/10 pt-4">
                    <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">⚙</span>
                        Configurações
                    </a>
                    <a href="#" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm text-slate-200/90 transition hover:bg-white/5">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/5">↩</span>
                        Sair
                    </a>
                </div>
            </aside>

            <main class="flex-1 p-4 md:p-6 xl:p-8">
                <header class="flex items-center justify-end gap-3 rounded-2xl border border-slate-200 bg-[#f7f7f5] px-4 py-4 shadow-sm sm:px-5">
                    <div class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white shadow-sm">
                        <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=200&q=80" alt="Perfil do usuário">
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-slate-800">{{ $user?->name ?? 'Pedro Alves Cabral' }}</div>
                        <div class="text-xs text-slate-500">{{ $roleLabel }}</div>
                    </div>
                </header>

                <section class="mt-8 px-1">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-[#f7f7f7] shadow-sm">
                        <div class="grid gap-0 xl:grid-cols-[1.7fr_0.9fr]">
                            <div class="p-6 md:p-8 lg:p-10">
                                <h1 class="text-4xl font-black tracking-tight text-slate-900">Gerar Relatório</h1>
                                <p class="mt-3 text-lg text-slate-600">Selecione os filtros para gerar um relatório personalizado</p>

                                <div class="mt-8 space-y-6">
                                    <div>
                                        <label class="mb-3 block text-lg font-medium text-slate-700">Período</label>
                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <label class="mb-2 block text-sm text-slate-600">Data inicial</label>
                                                <div class="flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-3 shadow-sm">
                                                    <span class="text-slate-500">01/08/2026</span>
                                                    <span class="ml-auto text-slate-400">📅</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="mb-2 block text-sm text-slate-600">Data final</label>
                                                <div class="flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-3 shadow-sm">
                                                    <span class="text-slate-500">24/08/2026</span>
                                                    <span class="ml-auto text-slate-400">📅</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="mb-3 block text-lg font-medium text-slate-700">Status da Ocorrência</label>
                                        <select class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base text-slate-700 shadow-sm focus:border-slate-400 focus:outline-none">
                                            <option>Todos os Status</option>
                                            <option>Pendente</option>
                                            <option>Em análise</option>
                                            <option>Resolvida</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-3 block text-lg font-medium text-slate-700">Categoria</label>
                                        <select class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base text-slate-700 shadow-sm focus:border-slate-400 focus:outline-none">
                                            <option>Todas as Categorias</option>
                                            <option>Buraco na via</option>
                                            <option>Iluminação pública</option>
                                            <option>Acúmulo de lixo</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-3 block text-lg font-medium text-slate-700">Órgão responsável</label>
                                        <select class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-base text-slate-700 shadow-sm focus:border-slate-400 focus:outline-none">
                                            <option>Todos os Órgãos</option>
                                            <option>Secretaria de Obras</option>
                                            <option>Iluminação pública</option>
                                            <option>Limpeza urbana</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-3 block text-lg font-medium text-slate-700">Prioridade</label>
                                        <div class="flex flex-wrap items-center gap-4 text-base text-slate-700">
                                            <label class="inline-flex items-center gap-2">
                                                <input type="radio" name="priority" checked class="h-4 w-4 accent-slate-800">
                                                <span>Todos</span>
                                            </label>
                                            <label class="inline-flex items-center gap-2">
                                                <input type="radio" name="priority" class="h-4 w-4 accent-slate-800">
                                                <span>Baixa</span>
                                            </label>
                                            <label class="inline-flex items-center gap-2">
                                                <input type="radio" name="priority" class="h-4 w-4 accent-slate-800">
                                                <span>Média</span>
                                            </label>
                                            <label class="inline-flex items-center gap-2">
                                                <input type="radio" name="priority" class="h-4 w-4 accent-slate-800">
                                                <span>Alta</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 border-t border-slate-300 pt-6">
                                    <div class="flex flex-col gap-3 sm:flex-row">
                                        <button class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-400 bg-white px-6 py-3 text-base font-semibold text-slate-800 shadow-sm">
                                            <span>⟳</span>
                                            Limpar Filtros
                                        </button>
                                        <button class="inline-flex items-center justify-center rounded-xl border border-slate-400 bg-[#d97706] px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-[#b45309]">
                                            Gerar Relatório
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <aside class="border-l border-slate-200 bg-[#1d3b34] p-6 text-white md:p-8">
                                <div class="rounded-2xl border border-[#dfe7e2] bg-[#f3f5f3] p-5 shadow-sm">
                                    <h2 class="text-2xl font-black text-slate-900">Sobre Os Relatórios</h2>
                                    <ul class="mt-5 space-y-4 text-base leading-7 text-slate-600">
                                        <li>• Gere relatórios com base nos dados registrados no sistema.</li>
                                        <li>• Você poderá usar os filtros para delimitar as informações.</li>
                                        <li>• Os relatórios podem ser exportados em PDF ou CSV.</li>
                                    </ul>
                                </div>

                                <div class="mt-8 rounded-2xl border border-[#dfe7e2] bg-[#f3f5f3] p-5 text-slate-800 shadow-sm">
                                    <h3 class="text-2xl font-black text-slate-900">Exemplos De Problemas</h3>
                                    <div class="mt-5 space-y-4 text-base text-slate-700">
                                        <div class="flex items-center gap-3"><span>🧱</span><span>Buraco na Via</span></div>
                                        <div class="flex items-center gap-3"><span>💡</span><span>Iluminação Pública</span></div>
                                        <div class="flex items-center gap-3"><span>🚰</span><span>Vazamento de Água</span></div>
                                        <div class="flex items-center gap-3"><span>🗑️</span><span>Lixo Acumulado</span></div>
                                        <div class="flex items-center gap-3"><span>📍</span><span>Outros</span></div>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</x-app-layout>
