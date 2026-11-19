<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.index', 'complaints.create', 'complaints.store', 'complaints.show')" wire:navigate>
                        Denúncias
                    </x-nav-link>

                    <x-nav-link :href="route('complaints.mine')" :active="request()->routeIs('complaints.mine', 'complaints.edit', 'complaints.update', 'complaints.destroy')" wire:navigate>
                        Minhas denúncias
                    </x-nav-link>

                    @can('manage-agents')
                        <x-nav-link :href="route('agents.index')" :active="request()->routeIs('agents.*')" wire:navigate>
                            {{ __('Agents') }}
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-theme-toggle />
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            @if (auth()->user()->profile_image)
                                <img data-profile-avatar data-image-url="{{ route('profile.image') }}" src="{{ route('profile.image', ['v' => auth()->user()->updated_at?->timestamp ?? time()]) }}" alt="Foto de {{ auth()->user()->name }}" class="me-2 h-8 w-8 rounded-full object-cover">
                            @else
                                <span data-profile-avatar data-image-url="{{ route('profile.image') }}" data-avatar-class="me-2 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-800 text-xs font-semibold text-white" class="me-2 flex h-8 w-8 items-center justify-center rounded-full bg-emerald-800 text-xs font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-3 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('complaints.index')" :active="request()->routeIs('complaints.index', 'complaints.create', 'complaints.store', 'complaints.show')" wire:navigate>
                Denúncias
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('complaints.mine')" :active="request()->routeIs('complaints.mine', 'complaints.edit', 'complaints.update', 'complaints.destroy')" wire:navigate>
                Minhas denúncias
            </x-responsive-nav-link>

            @can('manage-agents')
                <x-responsive-nav-link :href="route('agents.index')" :active="request()->routeIs('agents.*')" wire:navigate>
                    {{ __('Agents') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4 pb-3"><x-theme-toggle /></div>
            <div class="px-4">
                <div class="flex items-center gap-2 font-medium text-base text-gray-800 dark:text-gray-200">
                    @if (auth()->user()->profile_image)
                        <img data-profile-avatar data-image-url="{{ route('profile.image') }}" src="{{ route('profile.image', ['v' => auth()->user()->updated_at?->timestamp ?? time()]) }}" alt="Foto de {{ auth()->user()->name }}" class="h-9 w-9 rounded-full object-cover">
                    @else
                        <span data-profile-avatar data-image-url="{{ route('profile.image') }}" data-avatar-class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-800 text-xs font-semibold text-white" class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-800 text-xs font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    @endif
                    <span x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>
                </div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
