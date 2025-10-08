<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Renter: Set Up Cosmic Store Profile 🏪
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 space-y-8 border-t-4 border-pink-500">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- PUBLIC STORE DETAILS --}}
                    <div>
                        <h3 class="text-xl font-bold text-pink-400 border-b border-pink-700 pb-2 mb-4">1. Public Store
                            Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="store_name" :value="__('Store Name')" class="text-gray-300" />
                                <x-text-input id="store_name"
                                    class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                                    name="store_name" required autofocus />
                            </div>
                            <div>
                                <x-input-label for="logo" :value="__('Store Logo/Profile Picture')"
                                    class="text-gray-300" />
                                <input id="logo"
                                    class="block mt-1 w-full text-sm text-gray-200 border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none"
                                    type="file" name="logo" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Store Description (Public)')"
                                class="text-gray-300" />
                            <textarea id="description"
                                class="block mt-1 w-full border-gray-600 bg-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                name="description" rows="3"></textarea>
                        </div>
                    </div>

                    {{-- PRIVATE OWNER/ACCOUNT DETAILS --}}
                    <div>
                        <h3 class="text-xl font-bold text-pink-400 border-b border-pink-700 pb-2 mb-4 mt-8">2. Private
                            Owner Details & Security</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="owner_name" :value="__('Owner Name')" class="text-gray-300" />
                                <x-text-input id="owner_name"
                                    class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                                    name="owner_name" required />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('Phone Number')" class="text-gray-300" />
                                <x-text-input id="phone"
                                    class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                                    name="phone" required />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('Address')" class="text-gray-300" />
                                <x-text-input id="address"
                                    class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                                    name="address" required />
                            </div>
                        </div>

                        <h4 class="text-lg font-bold text-gray-300 mt-6 mb-3 border-b border-gray-700 pb-2">Password
                            Reset</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="old_password" :value="__('Old Password')" class="text-gray-400" />
                                <x-text-input id="old_password"
                                    class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="password"
                                    name="old_password" />
                            </div>
                            <div>
                                <x-input-label for="new_password" :value="__('New Password')" class="text-gray-400" />
                                <x-text-input id="new_password"
                                    class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="password"
                                    name="new_password" />
                            </div>
                            <div>
                                <x-input-label for="new_password_confirmation" :value="__('Confirm New Password')"
                                    class="text-gray-400" />
                                <x-text-input id="new_password_confirmation"
                                    class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="password"
                                    name="new_password_confirmation" />
                            </div>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">
                            <a href="#" class="text-indigo-400 hover:text-indigo-600">Forgot Password? (Use OTP)</a>
                        </p>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="bg-pink-600 hover:bg-pink-700">
                            {{ __('Save Profile & Details') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>