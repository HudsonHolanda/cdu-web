<?php

use App\Models\User;
use App\Rules\Cpf;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $cpf = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $cleanCpf = Cpf::digits($this->cpf);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],

            'cpf' => [
                'required',
                'string',
                new Cpf,
                function ($attribute, $value, $fail) use ($cleanCpf) {
                    if (User::where('cpf', $cleanCpf)->exists()) {
                        $fail('Este CPF já está cadastrado.');
                    }
                },
            ],

            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Store the CPF using digits only
        $validated['cpf'] = $cleanCpf;

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(
            route('dashboard', absolute: false),
            navigate: true
        );
    }
}; ?>

<div>
    <form wire:submit="register">
        <div class="shrink-0 flex items-center justify-center">
            <a href="{{ route('dashboard') }}" wire:navigate>
                <x-application-logo class="block h-14 w-auto" />
            </a>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- CPF -->
        <div class="mt-4">
            <x-input-label for="cpf" :value="__('CPF')" />
            <x-text-input wire:model="cpf" x-data="cpfMask" id="cpf" class="block mt-1 w-full" type="text" name="cpf" required maxlength="14" placeholder="000.000.000-00" inputmode="numeric" autocomplete="off" />
            <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
        </div>


        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-end gap-3 mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
