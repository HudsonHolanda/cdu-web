<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Agentes
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
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

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
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
                                <div class="grid grid-cols-2 gap-3">
                                    <a href="{{ route('agents.edit', $agent) }}" class="inline-flex items-center justify-center min-h-[44px] px-4 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest">Editar</a>
                                    <form method="POST" action="{{ route('agents.destroy', $agent) }}" onsubmit="return confirm('Remover o agente {{ addslashes($agent->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button class="w-full">Remover</x-danger-button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Tablet e desktop: tabela --}}
                    <table class="hidden md:table w-full text-sm text-start">
                        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-start font-medium">Nome</th>
                                <th class="px-4 py-3 text-start font-medium">E-mail</th>
                                <th class="px-4 py-3 text-start font-medium">CPF</th>
                                <th class="px-4 py-3 text-end font-medium">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-900 dark:text-gray-100">
                            @foreach ($agents as $agent)
                                <tr>
                                    <td class="px-4 py-3">{{ $agent->name }}</td>
                                    <td class="px-4 py-3 break-all">{{ $agent->email }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $agent->cpf) }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('agents.edit', $agent) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs uppercase tracking-widest">Editar</a>
                                            <form method="POST" action="{{ route('agents.destroy', $agent) }}" onsubmit="return confirm('Remover o agente {{ addslashes($agent->name) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button>Remover</x-danger-button>
                                            </form>
                                        </div>
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
