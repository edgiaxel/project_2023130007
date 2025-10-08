<x-guest-layout>
    <div class="flex flex-col items-center justify-center p-6 text-center text-white space-y-2"
        style="background-color: #1a0046; border-radius: 0.5rem 0.5rem 0 0;">
        <h1 class="text-3xl font-extrabold text-indigo-400">Welcome back, Traveler!</h1>
        <p class="text-sm text-gray-300">Log in to manage your cosmic journey with **Starium Rental**.</p>
    </div>

    <div class="p-6 bg-gray-800 shadow-xl sm:rounded-lg">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="mt-4 space-y-6">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email (Cosmic ID)')" class="text-indigo-400" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="email"
                    name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password (Warp Key)')" class="text-indigo-400" />
                <x-text-input id="password" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                    type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="block">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded dark:bg-gray-700 border-gray-600 text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        name="remember">
                    <span class="ms-2 text-sm text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-6">
                @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-400 hover:text-white" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
                @endif

                <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                    {{ __('Engage!') }}
                </x-primary-button>
            </div>

            <p class="text-center text-sm text-gray-500 pt-4 border-t border-gray-700">
                New to the cosmos? <a href="{{ route('register') }}"
                    class="text-pink-400 hover:text-pink-300 font-semibold">Register Here</a>.
            </p>
        </form>
    </div>
</x-guest-layout>