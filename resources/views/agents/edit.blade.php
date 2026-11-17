<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Administração</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Editar agente</h1>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <x-agent-form :agent="$agent" :action="route('agents.update', $agent)" method="PUT" submit="Atualizar agente" />
        </div>
    </div>
</x-app-layout>
