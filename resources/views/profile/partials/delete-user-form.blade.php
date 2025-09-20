<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-[#5a3e2b] dark:text-[#f6e7d8]">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-[#7c5a44] dark:text-[#d5c2b3]">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    {{-- Delete Button --}}
    <x-danger-button
        class="bg-[#d96e5f] hover:bg-[#c65a4a] text-white rounded-lg shadow-md"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        {{ __('Delete Account') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-[#fff8f0] dark:bg-[#3b2b24] rounded-xl">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-[#5a3e2b] dark:text-[#f6e7d8]">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-[#7c5a44] dark:text-[#d5c2b3]">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="{{ __('Password') }}"
                    class="mt-1 block w-3/4 border border-[#e6cbb3] dark:border-[#6e4f3a] bg-[#fff8f0] dark:bg-[#3b2b24] text-[#5a3e2b] dark:text-[#f6e7d8] rounded-lg focus:ring-2 focus:ring-[#e59f71] focus:border-[#e59f71]"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-[#b33a3a]" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button
                    class="bg-[#e6cbb3] hover:bg-[#d5b59a] text-[#5a3e2b] dark:bg-[#6e4f3a] dark:hover:bg-[#5a3e2b] dark:text-[#f6e7d8] rounded-lg shadow-md"
                    x-on:click="$dispatch('close')"
                >
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button
                    class="ms-3 bg-[#d96e5f] hover:bg-[#c65a4a] text-white rounded-lg shadow-md"
                >
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>

