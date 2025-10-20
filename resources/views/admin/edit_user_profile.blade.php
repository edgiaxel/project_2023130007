<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Edit Profile for <span class="text-indigo-400">{{ $user->name }}</span> (ID: {{ $user->id }})
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. USER PROFILE INFO --}}
            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border-t-4 border-indigo-500">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Core Account Details (Admin Edit)') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Edit personal, contact, and profile image information.") }}
                        </p>
                    </header>
                
                    <form method="post" action="{{ route('admin.users.update', $user->id) }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        
                        {{-- Name and Email (Standard) --}}
                        <div>
                            <x-input-label for="name" :value="__('User/Account Name')" class="text-indigo-400" />
                            <x-text-input id="name" name="name" type="text"
                                class="mt-1 block w-full bg-gray-700 border-gray-600 text-white" :value="old('name', $user->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-indigo-400" />
                            <x-text-input id="email" name="email" type="email"
                                class="mt-1 block w-full bg-gray-700 border-gray-600 text-white" :value="old('email', $user->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        {{-- Profile Picture --}}
                        <div>
                            <x-input-label for="profile_picture" :value="__('Profile Picture')" class="text-indigo-400" />
                            <input id="profile_picture" name="profile_picture" type="file"
                                class="block mt-1 w-full text-sm text-gray-200 border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none" />
                            <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                            @if($user->profile_picture)
                                <p class="text-xs text-gray-500 mt-1">Current File: {{ basename($user->profile_picture) }}</p>
                            @endif
                        </div>

                        {{-- Contact Details --}}
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
                            <x-primary-button class="bg-red-600 hover:bg-red-700">{{ __('Save Changes (Admin Override)') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
            
            {{-- 2. RENTER STORE SETUP --}}
            @if ($user->hasRole('renter'))
                <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border-t-4 border-pink-500">
                    <div class="max-w-xl">
                         <h2 class="text-xl font-medium text-pink-400 border-b border-pink-700 pb-2 mb-4">
                             {{ __('Renter Shop & Store Details (Admin Edit)') }}
                         </h2>
                         @include('profile.partials.renter-store-form', ['user' => $user])
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>