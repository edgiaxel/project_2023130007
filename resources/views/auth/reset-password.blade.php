<x-guest-layout>
    <div class="flex flex-col items-center justify-center p-6 text-center text-white space-y-2"
        style="background-color: #1a0046; border-radius: 0.5rem 0.5rem 0 0;">
        <h1 class="text-3xl font-extrabold text-pink-400">Reset Warp Key</h1>
    </div>

    <div class="p-6 bg-gray-800 shadow-xl sm:rounded-lg">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            {{-- 💥 CRITICAL: Keep the token here! --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('Cosmic ID (Email)')" class="text-indigo-400" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-900 border-gray-600 text-gray-400" 
                    type="email" name="email" :value="old('email', $request->email)" required readonly />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="otp" :value="__('6-Digit Code')" class="text-yellow-400" />
                <x-text-input id="otp" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                    type="text" name="otp" required autofocus placeholder="Enter 6-digit OTP" />
                <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('New Warp Key (Password)')" class="text-indigo-400" />
                <x-text-input id="password" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                    type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm New Key')" class="text-indigo-400" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" 
                    type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button class="bg-pink-600 hover:bg-pink-700">
                    {{ __('Reset Warp Key') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>