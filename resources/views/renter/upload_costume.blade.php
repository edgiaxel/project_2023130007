<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Renter: Upload New Cosmic Costume 🚀
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-pink-500">
                <form action="#" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <x-input-label for="character_name" :value="__('Character Name')" class="text-pink-400" />
                        <x-text-input id="character_name"
                            class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                            name="character_name" required autofocus />
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="media_series" :value="__('Media Series (Anime/Game/Book)')"
                                class="text-pink-400" />
                            <x-text-input id="media_series"
                                class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                                name="media_series" required />
                        </div>
                        <div>
                            <x-input-label for="size" :value="__('Size (e.g., M, L, Custom)')" class="text-pink-400" />
                            <x-text-input id="size" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="text" name="size" required />
                        </div>
                    </div>

                    {{-- Price and Condition --}}
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="price" :value="__('Price (Rp)')" class="text-pink-400" />
                            <x-text-input id="price" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="number" name="price" required />
                        </div>
                        <div>
                            <x-input-label for="rental_period" :value="__('Price Per...')" class="text-pink-400" />
                            <select id="rental_period" name="rental_period"
                                class="block mt-1 w-full border-gray-600 bg-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="day">Day</option>
                                <option value="week">Week</option>
                                <option value="month">Month</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="condition" :value="__('Condition')" class="text-pink-400" />
                            <select id="condition" name="condition"
                                class="block mt-1 w-full border-gray-600 bg-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="new">New</option>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="worn">Worn</option>
                            </select>
                        </div>
                    </div>

                    {{-- Stock, Tags, and Images --}}
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="stock" :value="__('Stock')"
                                class="text-pink-400" />
                            <x-text-input id="stock" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="number" name="stock" value="1" required />
                        </div>
                        <div>
                            <x-input-label for="tags" :value="__('Tags (Comma Separated: Naruto, Kimono, Armor)')"
                                class="text-pink-400" />
                            <x-text-input id="tags" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="text" name="tags" placeholder="e.g., Fantasy, Armor, Event Promo" required />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="images" :value="__('Costume Images (Multiple allowed)')"
                            class="text-pink-400" />
                        <input id="images"
                            class="block mt-1 w-full text-sm text-gray-200 border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none"
                            type="file" name="images[]" multiple required />
                    </div>

                    <p class="text-sm text-yellow-400 mt-2">NOTE: This costume will require Admin Approval before
                        showing in the catalog.</p>

                    <div class="flex items-center justify-end">
                        <x-primary-button class="bg-pink-600 hover:bg-pink-700">
                            {{ __('Submit Costume for Approval') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>