<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Agentes
        </h2>
    </x-slot>

    <div class="agents-index-page py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div role="status" class="p-4 rounded-md bg-green-50 text-green-800 dark:bg-green-900/40 dark:text-green-200 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Novo agente: já abre quando houve erro de validação --}}
            <details class="bg-white dark:bg-gray-800 shadow sm:rounded-lg" @if ($errors->any() && ! request()->routeIs('agents.edit')) open @endif>
                <summary class="cursor-pointer select-none p-4 sm:p-6 min-h-[44px] font-medium text-gray-900 dark:text-gray-100">
                    + Cadastrar novo agente
                </summary>
                <div class="px-4 pb-4 sm:px-6 sm:pb-6">
                    <x-agent-form :action="route('agents.store')" submit="Cadastrar agente" />
                </div>
            </details>

            <div class="agents-list-card overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-gray-800">
                @if ($agents->isEmpty())
                    <p class="p-6 text-sm text-gray-600 dark:text-gray-400">Nenhum agente cadastrado ainda.</p>
                @else
                    {{-- Celular: cartões --}}
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700 md:hidden">
                        @foreach ($agents as $agent)
                            <li class="p-4 space-y-3">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-gray-100 break-words">{{ $agent->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 break-all">{{ $agent->email }}</p>
                                    <p class="text-sm text-gray-500">CPF {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $agent->cpf) }}</p>
                                </div>
                                @admin
                                    <div class="grid grid-cols-2 gap-3">
                                        <a href="{{ route('agents.edit', $agent) }}" class="inline-flex items-center justify-center min-h-[44px] px-4 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest">Editar</a>
                                        <form method="POST" action="{{ route('agents.destroy', $agent) }}" onsubmit="return confirm('Remover o agente {{ addslashes($agent->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button class="w-full">Remover</x-danger-button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">Somente visualização</span>
                                @endadmin
                            </li>
                        @endforeach
                    </ul>

                    {{-- Tablet e desktop: tabela --}}
                    <div class="agents-list-heading hidden items-center justify-between gap-4 border-b border-gray-100 px-6 py-5 dark:border-gray-700 md:flex">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Agentes cadastrados</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Consulte os agentes autorizados a operar o painel.</p>
                        </div>
                        <span class="agents-count-badge">{{ $agents->total() }} {{ $agents->total() === 1 ? 'agente' : 'agentes' }}</span>
                    </div>
                    <table class="agents-table hidden w-full text-sm text-start md:table">
                        <thead class="bg-gray-50/80 text-gray-600 dark:bg-gray-700/70 dark:text-gray-300">
                            <tr>
                                <th class="px-6 py-3 text-start font-medium">Agente</th>
                                <th class="px-6 py-3 text-start font-medium">Contato</th>
                                <th class="px-6 py-3 text-start font-medium">CPF</th>
                                <th class="px-6 py-3 text-start font-medium">Cadastro</th>
                                <th class="px-6 py-3 text-end font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-900 dark:text-gray-100">
                            @foreach ($agents as $agent)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="agents-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($agent->name, 0, 1)) }}</span>
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold">{{ $agent->name }}</p>
                                                <span class="agents-role-badge">Agente</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="block max-w-[18rem] break-all text-gray-600 dark:text-gray-300">{{ $agent->email }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $agent->cpf) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300">{{ optional($agent->created_at)->format('d/m/Y') ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        @admin
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('agents.edit', $agent) }}" class="agents-action-button agents-action-button--edit">Editar</a>
                                                <form method="POST" action="{{ route('agents.destroy', $agent) }}" onsubmit="return confirm('Remover o agente {{ addslashes($agent->name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="agents-action-button agents-action-button--remove">Remover</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Somente visualização</span>
                                        @endadmin
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($agents->hasPages())
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">{{ $agents->links() }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
