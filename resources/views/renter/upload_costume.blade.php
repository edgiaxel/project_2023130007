<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Renter: Upload New Cosmic Costume 🚀
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-pink-500">
                <form action="{{ route('renter.costumes.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
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

                    {{-- Price and Condition (Removed redundant rental_period dropdown) --}}
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="price" :value="__('Price per Day (Rp)')" class="text-pink-400" />
                            <x-text-input id="price" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="number" name="price" required />
                        </div>

                        {{-- 💥 FIX: Add Stock Input (was missing, causing validation failure) --}}
                        <div>
                            <x-input-label for="stock" :value="__('Stock Count')" class="text-pink-400" />
                            <x-text-input id="stock" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="number" name="stock" value="1" required />
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

                    {{-- Tags and Images --}}
                    <div class="grid grid-cols-1 gap-6">
                        {{-- The original layout included redundant stock here, consolidating above. --}}
                        <div>
                            <x-input-label for="tags" :value="__('Tags (Comma Separated)')" class="text-pink-400" />
                            <x-text-input id="tags" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="text" name="tags" placeholder="e.g., Fantasy, Armor, Event Promo" required />
                        </div>
                    </div>

                    {{-- 💥 NEW: DISCOUNT SECTION --}}
                    <h4 class="text-xl font-bold text-yellow-400 border-b border-gray-700 pt-4 pb-2">Optional Discount
                    </h4>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="discount_value" :value="__('Discount Value')" class="text-yellow-400" />
                            <x-text-input id="discount_value"
                                class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="number"
                                name="discount_value" value="0" min="0" />
                        </div>
                        <div>
                            <x-input-label for="discount_type" :value="__('Type')" class="text-yellow-400" />
                            <select id="discount_type" name="discount_type"
                                class="block mt-1 w-full border-gray-600 bg-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Price (Rp)</option>
                            </select>
                        </div>
                    </div>

                    {{-- 💥 REPLACED CHECKBOX WITH DROPDOWN --}}
                    <div class="mt-4">
                        <x-input-label for="is_discount_active_edit" :value="__('Discount Visibility Status')"
                            class="text-pink-400" />
                        <select name="is_discount_active" id="is_discount_active_edit"
                            class="block mt-1 w-full border-gray-600 bg-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="1" @selected(old('is_discount_active', $costume->is_discount_active) == 1)>
                                ✅ ACTIVE (Discount is live for customers)
                            </option>
                            <option value="0" @selected(old('is_discount_active', $costume->is_discount_active) == 0)>
                                ❌ INACTIVE (Hide discount, show original price)
                            </option>
                        </select>

                        @if ($costume->is_discount_active)
                            <p class="text-xs text-green-400 mt-1 font-bold">✨ Current status: LIVE in catalog!</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1 italic">Note: Customers will see the standard price.</p>
                        @endif
                    </div>



                    {{-- 💥 CRITICAL FIX: The image upload field uses 'images[]' for multiple files, but the controller
                    validation expects 'images' max:1. The controller must be updated, or we switch the form field to a
                    single name --}}
                    <div>
                        <x-input-label for="images" :value="__('Costume Images (Multiple Files)')"
                            class="text-pink-400" />
                        <input id="images"
                            class="block mt-1 w-full text-sm text-gray-200 border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none"
                            type="file" name="images[]" multiple required accept="image/jpeg, image/png, image/jpg" />
                        <p class="text-xs text-gray-400 mt-1">Select 1 to 5 image files (JPG/PNG, max 4MB each).</p>
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