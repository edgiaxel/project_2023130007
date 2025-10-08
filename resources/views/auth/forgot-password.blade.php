<x-guest-layout>
    <div class="flex flex-col items-center justify-center p-6 text-center text-white space-y-2"
        style="background-color: #1a0046; border-radius: 0.5rem 0.5rem 0 0;">
        <h1 class="text-3xl font-extrabold text-yellow-400">Lost Your Warp Key?</h1>
    </div>

    <div class="p-6 bg-gray-800 shadow-xl sm:rounded-lg">
        <div class="mb-4 text-sm text-gray-400">
            {{ __('No worries, space traveler. Enter your Cosmic ID (email) and we\'ll send you a password reset link.') }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email Address')" class="text-indigo-400" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="email"
                    name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button class="bg-yellow-600 hover:bg-yellow-700">
                    {{ __('Send Reset Link') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>