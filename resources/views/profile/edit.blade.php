<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Cosmic Account Settings') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. STANDARD PROFILE INFO --}}
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border-t-4 border-indigo-500">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 2. RENTER STORE SETUP --}}
            @if (Auth::user()->hasRole('renter'))
                <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border-t-4 border-pink-500">
                    <div class="max-w-xl">
                        <h2 class="text-xl font-medium text-pink-400 border-b border-pink-700 pb-2 mb-4">
                            {{ __('Renter Shop & Store Details') }}
                        </h2>
                        @include('profile.partials.renter-store-form')
                    </div>
                </div>
            @endif

            {{-- 3. PASSWORD UPDATE --}}
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border-t-4 border-yellow-500">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 4. DELETE ACCOUNT --}}
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border-t-4 border-red-500">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>