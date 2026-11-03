@props(['agent' => null, 'action', 'method' => 'POST', 'submit' => 'Salvar'])

<form method="POST" action="{{ $action }}" class="space-y-4">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="name" value="Nome" />
            <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $agent?->name)" required autocomplete="off" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input id="email" name="email" type="email" inputmode="email" class="block mt-1 w-full" :value="old('email', $agent?->email)" required autocomplete="off" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="cpf" value="CPF" />
            <x-text-input id="cpf" name="cpf" type="text" x-data="cpfMask" class="block mt-1 w-full" :value="old('cpf', $agent ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $agent->cpf) : '')" required maxlength="14" placeholder="000.000.000-00" inputmode="numeric" autocomplete="off" />
            <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
        </div>

        <div class="hidden md:block"></div>

        <div>
            <x-input-label for="password" :value="$agent ? 'Nova senha (deixe em branco para manter)' : 'Senha'" />
            <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" :required="! $agent" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar senha" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" :required="! $agent" autocomplete="new-password" />
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        @if ($agent)
            <a href="{{ route('agents.index') }}" class="inline-flex items-center justify-center min-h-[44px] sm:min-h-0 px-4 py-2 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</a>
        @endif
        <x-primary-button class="w-full sm:w-auto">{{ $submit }}</x-primary-button>
    </div>
</form>
