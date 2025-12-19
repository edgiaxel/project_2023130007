<x-guest-layout>
    <div class="flex flex-col items-center justify-center p-6 text-center text-white space-y-2"
        style="background-color: #1a0046; border-radius: 0.5rem 0.5rem 0 0;">
        <h1 class="text-3xl font-extrabold text-indigo-400">Welcome back, Traveler!</h1>
        <p class="text-sm text-gray-300">Log in to manage your cosmic journey with Starium Rental.</p>
    </div>

    <div class="p-6 bg-gray-800 shadow-xl sm:rounded-lg" x-data="{
    debugAccounts: {
        'Owner': 'owner@starium.test',
        'Admin': 'admin@starium.test',
        'User': 'user@starium.test',
        'Captain Cosmic - Cosmic Thread': 'renter1@starium.test',
        'Princess Aurora - Fairy Dust': 'renter2@starium.test',
        'The Anime King - Weeb Central': 'renter3@starium.test',
    },
    selectedAccount: '',
    fillLogin() {
        if (this.selectedAccount) {
            // Set the email input value
            document.getElementById('email').value = this.debugAccounts[this.selectedAccount];
            // Set the password input value (The hardcoded password is 'password')
            document.getElementById('password').value = 'password';
        }
    }
}">
        <x-auth-session-status class="mb-4" :status="session('status')" />

        {{-- DEBUG DROPDOWN START --}}
        <div class="mb-6 p-4 bg-gray-700 rounded-lg border border-red-500">
            <label for="debug_user" class="block text-sm font-medium text-red-400 mb-2">🚨 DEBUG: Quick Fill
                Accounts</label>
            <select id="debug_user" x-model="selectedAccount" x-on:change="fillLogin()"
                class="block w-full bg-gray-600 border-gray-500 text-white focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm">
                <option value="">-- Select an Account to Auto-Fill --</option>
                <template x-for="(email, name) in debugAccounts" :key="name">
                    <option :value="name" x-text="`${name} (${email})`"></option>
                </template>
            </select>
        </div>
        {{-- DEBUG DROPDOWN END --}}

        <form method="POST" action="{{ route('login') }}" class="mt-4 space-y-6">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email (Cosmic ID)')" class="text-indigo-400" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="email"
                    name="email" :value="old('email')" required autofocus autocomplete="off" /> <x-input-error
                    :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password (Warp Key)')" class="text-indigo-400" />
                <x-text-input id="password" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                    type="password" name="password" required autocomplete="off" /> <x-input-error
                    :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="block">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded dark:bg-gray-700 border-gray-600 text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        name="remember">
                    <span class="ms-2 text-sm text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div>
                <x-input-label for="captcha" :value="__('Security Check')" class="text-indigo-400" />
                <div class="flex items-center space-x-3 mt-1" x-data="{ captchaSrc: '{{ captcha_src('flat') }}' }">
                    <div class="flex space-x-2 items-center shrink-0">
                        <div
                            class="w-40 h-auto bg-white rounded-md overflow-hidden flex items-center justify-center border-2 border-indigo-500">
                            {{-- 💥 Use a dynamic :src so Alpine can force a reload --}}
                            <img :src="captchaSrc" alt="captcha" class="w-full h-full object-cover">
                        </div>
                        <button type="button" @click="captchaSrc = '{{ captcha_src('flat') }}' + Math.random()"
                            class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition duration-150 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                    <x-text-input id="captcha" class="block w-full bg-gray-700 border-gray-600 text-white" type="text"
                        name="captcha" required autocomplete="off" placeholder="Type the code" />
                </div>
                <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
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



{{--taro diatas block--}}