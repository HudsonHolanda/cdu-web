<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $profileImage;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->getKey(), 'id_user')],
            'profileImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->profileImage) {
            if ($user->profile_image) {
                Storage::disk(config('filesystems.profile'))->delete($user->profile_image);
                // Remove files created by the previous hard-coded public disk.
                if (config('filesystems.profile') !== 'public') {
                    Storage::disk('public')->delete($user->profile_image);
                }
            }

            $user->profile_image = $this->profileImage->store('profile-images', config('filesystems.profile'));
        }

        $user->save();

        $this->reset('profileImage');

        $this->dispatch('profile-updated', name: $user->name, avatarVersion: now()->timestamp);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="profileImage" :value="__('Foto de perfil')" />
            <div class="mt-2 flex items-center gap-4">
                @if (auth()->user()->profile_image)
                    <img data-profile-avatar data-image-url="{{ route('profile.image') }}" src="{{ route('profile.image', ['v' => auth()->user()->updated_at?->timestamp ?? time()]) }}" alt="Foto de perfil" class="h-16 w-16 rounded-full object-cover ring-2 ring-gray-200">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-800 text-xl font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                @endif
                <input wire:model="profileImage" id="profileImage" name="profileImage" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-600 dark:text-gray-300">
            </div>
            <p class="mt-1 text-xs text-gray-500">JPG, PNG ou WebP, até 5 MB.</p>
            <x-input-error class="mt-2" :messages="$errors->get('profileImage')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
