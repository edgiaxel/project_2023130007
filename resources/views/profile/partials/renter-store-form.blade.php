<section>
    <p class="mt-1 text-sm text-gray-400">
        {{ __("Manage your public-facing store name, logo, and description. This is what customers see in the catalog.") }}
    </p>

    <form method="post" action="{{ route('renter.store.update') }}" enctype="multipart/form-data"
        class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Store Name --}}
        <div>
            <x-input-label for="store_name" :value="__('Store Name')" class="text-pink-400" />
            <x-text-input id="store_name" name="store_name" type="text"
                class="mt-1 block w-full bg-gray-700 border-gray-600 text-white" :value="old('store_name', $user->store->store_name ?? $user->name . ' Shop')" required />
            <x-input-error class="mt-2" :messages="$errors->get('store_name')" />
        </div>

        {{-- Store Logo --}}
        <div>
            <x-input-label for="store_logo" :value="__('Store Logo')" class="text-pink-400" />
            <input id="store_logo" name="store_logo" type="file"                
                class="block mt-1 w-full text-sm text-gray-200 border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none" />
            <x-input-error class="mt-2" :messages="$errors->get('store_logo')" />
            @if($user->store && $user->store->store_logo_path)
                <p class="text-xs text-gray-500 mt-1">
                    Current File: {{ basename($user->store->store_logo_path) }}
                </p>
            @endif
        </div>

        {{-- Store Description --}}
        <div>
            <x-input-label for="description" :value="__('Store Description (Public)')" class="text-pink-400" />
            <textarea id="description" name="description"
                class="mt-1 block w-full border-gray-600 bg-gray-700 text-white focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm">{{ old('description', $user->store->description ?? 'We rent the best costumes in the entire solar system!') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        {{-- Submit Button + Status --}}
        <div class="flex items-center gap-4">
            <x-primary-button class="bg-pink-600 hover:bg-pink-700">
                {{ __('Save Store Details') }}
            </x-primary-button>

            @if (session('status') === 'renter-store-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 dark:text-green-400">
                    {{ __('Store Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>