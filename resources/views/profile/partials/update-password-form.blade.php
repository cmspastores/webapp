<section>
    <header>
        <h2 class="text-lg font-medium text-[#5a3e2b] dark:text-[#f6e7d8]">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-[#7c5a44] dark:text-[#d5c2b3]">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-[#5a3e2b] dark:text-[#f6e7d8]" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full border border-[#e6cbb3] dark:border-[#6e4f3a] bg-[#fff8f0] dark:bg-[#3b2b24] text-[#5a3e2b] dark:text-[#f6e7d8] rounded-lg focus:ring-2 focus:ring-[#e59f71] focus:border-[#e59f71]"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-[#b33a3a]" />
        </div>

        {{-- New Password --}}
        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="text-[#5a3e2b] dark:text-[#f6e7d8]" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full border border-[#e6cbb3] dark:border-[#6e4f3a] bg-[#fff8f0] dark:bg-[#3b2b24] text-[#5a3e2b] dark:text-[#f6e7d8] rounded-lg focus:ring-2 focus:ring-[#e59f71] focus:border-[#e59f71]"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-[#b33a3a]" />
        </div>

        {{-- Confirm Password --}}
        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-[#5a3e2b] dark:text-[#f6e7d8]" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full border border-[#e6cbb3] dark:border-[#6e4f3a] bg-[#fff8f0] dark:bg-[#3b2b24] text-[#5a3e2b] dark:text-[#f6e7d8] rounded-lg focus:ring-2 focus:ring-[#e59f71] focus:border-[#e59f71]"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-[#b33a3a]" />
        </div>

        {{-- Save Button --}}
        <div class="flex items-center gap-4">
            <x-primary-button class="bg-[#e59f71] hover:bg-[#d5895c] text-white rounded-lg shadow">
                {{ __('Save') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-[#7c5a44] dark:text-[#d5c2b3]">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
