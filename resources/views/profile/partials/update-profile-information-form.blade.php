<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Cosmic Profile Details') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your basic contact and profile information for the platform.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Existing Name/Email Fields --}}
        <div>
            <x-input-label for="name" :value="__('User/Account Name')" class="text-indigo-400" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full bg-gray-700 border-gray-600 text-white" :value="old('name', $user->name)"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-indigo-400" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full bg-gray-700 border-gray-600 text-white" :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            {{-- ... email verification status ... --}}
        </div>

        {{-- NEW CUSTOM FIELDS --}}
        <div>
            <x-input-label for="profile_picture" :value="__('Public Profile Picture')" class="text-indigo-400" />
            <input id="profile_picture" name="profile_picture" type="file"
                class="block mt-1 w-full text-sm text-gray-200 border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none" />
            <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="phone_number" :value="__('Phone Number')" class="text-indigo-400" />
                <x-text-input id="phone_number" name="phone_number" type="text"
                    class="mt-1 block w-full bg-gray-700 border-gray-600 text-white" :value="old('phone_number', $user->phone_number)" />
                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
            </div>
            <div class="col-span-1 md:col-span-2">
                <x-input-label for="address" :value="__('Address')" class="text-indigo-400" />
                <x-text-input id="address" name="address" type="text"
                    class="mt-1 block w-full bg-gray-700 border-gray-600 text-white" :value="old('address', $user->address)" />
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">{{ __('Save Changes') }}</x-primary-button>
            {{-- ... status message ... --}}
        </div>
    </form>
</section>