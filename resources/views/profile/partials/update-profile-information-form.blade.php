<section>
    <header>
        <h2 class="text-lg font-medium text-[#5a3e2b] dark:text-[#f6e7d8]">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-[#7c5a44] dark:text-[#d5c2b3]">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" class="text-[#5a3e2b] dark:text-[#f6e7d8]" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full border border-[#e6cbb3] dark:border-[#6e4f3a] bg-[#fff8f0] dark:bg-[#3b2b24] text-[#5a3e2b] dark:text-[#f6e7d8] rounded-lg focus:ring-2 focus:ring-[#e59f71] focus:border-[#e59f71]"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-[#b33a3a]" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[#5a3e2b] dark:text-[#f6e7d8]" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full border border-[#e6cbb3] dark:border-[#6e4f3a] bg-[#fff8f0] dark:bg-[#3b2b24] text-[#5a3e2b] dark:text-[#f6e7d8] rounded-lg focus:ring-2 focus:ring-[#e59f71] focus:border-[#e59f71]"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 text-[#b33a3a]" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-[#7c5a44] dark:text-[#d5c2b3]">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-[#e59f71] hover:text-[#c97b50] dark:text-[#f3c8a2] dark:hover:text-[#f6e7d8] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#e59f71] dark:focus:ring-offset-[#3b2b24]">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-700 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-[#e59f71] hover:bg-[#d5895c] text-white rounded-lg shadow">
                {{ __('Save') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-[#7c5a44] dark:text-[#d5c2b3]">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
