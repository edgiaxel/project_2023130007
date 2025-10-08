<x-guest-layout>
    <div class="flex flex-col items-center justify-center p-6 text-center text-white space-y-2"
        style="background-color: #1a0046; border-radius: 0.5rem 0.5rem 0 0;">
        <h1 class="text-3xl font-extrabold text-pink-400">Join the Cosmic Crew!</h1>
        <p class="text-sm text-gray-300">Sign up to explore or launch your own rental shop.</p>
    </div>

    <div class="p-6 bg-gray-800 shadow-xl sm:rounded-lg">
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            {{-- Account Type Selection (DUMMY FOR MIDTERM) --}}
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