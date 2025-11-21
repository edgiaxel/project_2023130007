<x-guest-layout>
    <div class="flex flex-col items-center justify-center p-6 text-center text-white space-y-2"
        style="background-color: #1a0046; border-radius: 0.5rem 0.5rem 0 0;">
        <h1 class="text-3xl font-extrabold text-pink-400">Join the Cosmic Crew!</h1>
        <p class="text-sm text-gray-300">Sign up to explore or launch your own rental shop.</p>
    </div>

    <div class="p-6 bg-gray-800 shadow-xl sm:rounded-lg">
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <div class="border-b border-gray-700 pb-4">
                <x-input-label :value="__('Account Type')" class="text-pink-400" />
                <div class="mt-2 flex space-x-6 text-gray-300">
                    <label class="inline-flex items-center">
                        <input type="radio" name="role_type" value="user" checked
                            class="rounded dark:bg-gray-700 border-gray-600 text-indigo-500 focus:ring-indigo-500" />
                        <span class="ms-2 text-sm">{{ __('Renter (Customer)') }}</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="role_type" value="renter"
                            class="rounded dark:bg-gray-700 border-gray-600 text-pink-500 focus:ring-pink-500" />
                        <span class="ms-2 text-sm">{{ __('Provider (Costume Renter)') }}</span>
                    </label>
                </div>
            </div>

            <div>
                <x-input-label for="name" :value="__('Name')" class="text-indigo-400" />
                <x-text-input id="name" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                    name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-indigo-400" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="email"
                    name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-indigo-400" />
                <x-text-input id="password" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                    type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-indigo-400" />
                <x-text-input id="password_confirmation"
                    class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="pt-4 border-t border-gray-700">
                <x-input-label for="captcha" :value="__('Security Check')" class="text-indigo-400" />

                <div class="flex items-center space-x-3 mt-1">
                    <div class="flex space-x-2 items-center shrink-0">
                        <div id="captcha-img-container-register"
                            class="w-36 h-10 bg-gray-700 border border-gray-600 rounded-md overflow-hidden flex items-center justify-center">
                            **{!! captcha_img('flat') !!}**
                        </div>

                        <button type="button"
                            onclick="document.getElementById('captcha-img-container-register').querySelector('img').src = '{{ route('captcha.custom', ['flat']) }}?' + Math.random();"
                            class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full transition duration-150 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001m-4.992 0L16.023 21m-4.992-1.998v-.001m4.992-10.999L16.023 9m-4.992-4.998v.001m4.992 10.998v-.001m-10.999 4.992L3.024 16.023m1.992-4.992v.001m-1.992 4.992L3.024 16.023m1.992 4.992L3.024 16.023m1.992 4.992v.001m4.992-10.998v-.001" />
                            </svg>
                        </button>
                    </div>

                    <x-text-input id="captcha" class="block w-full bg-gray-700 border-gray-600 text-white" type="text"
                        name="captcha" required autocomplete="off" placeholder="Type the code above" />
                </div>

                <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-400 hover:text-white" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="ms-4 bg-pink-600 hover:bg-pink-700">
                    {{ __('Launch Account!') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>