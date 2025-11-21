<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Renter: Edit Cosmic Costume: <span class="text-pink-400">{{ $costume->name }}</span> 🚀
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-pink-500">
                <form action="{{ route('renter.costumes.update', $costume->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    {{-- Costume Status Indicator --}}
                    <p class="text-sm font-bold {{ $costume->is_approved ? 'text-green-400' : 'text-yellow-400' }} pb-2 border-b border-gray-700">
                        Status: {{ $costume->is_approved ? 'LIVE in Catalog' : 'PENDING ADMIN APPROVAL' }}
                    </p>

                    {{-- Costume Basic Details --}}
                    <div>
                        <x-input-label for="character_name" :value="__('Character Name')" class="text-pink-400" />
                        <x-text-input id="character_name"
                            class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                            name="character_name" value="{{ old('character_name', $costume->name) }}" required autofocus />
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="media_series" :value="__('Media Series (Anime/Game/Book)')"
                                class="text-pink-400" />
                            <x-text-input id="media_series"
                                class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                                name="media_series" value="{{ old('media_series', $costume->series) }}" required />
                        </div>
                        <div>
                            <x-input-label for="size" :value="__('Size (e.g., M, L, Custom)')" class="text-pink-400" />
                            <x-text-input id="size" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="text" name="size" value="{{ old('size', $costume->size) }}" required />
                        </div>
                    </div>

                    {{-- Price and Condition --}}
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="price" :value="__('Price per Day (Rp)')" class="text-pink-400" />
                            <x-text-input id="price" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="number" name="price" value="{{ old('price', $costume->price_per_day) }}" required />
                        </div>
                        <div>
                            <x-input-label for="stock" :value="__('Stock')" class="text-pink-400" />
                            <x-text-input id="stock" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                                type="number" name="stock" value="{{ old('stock', $costume->stock) }}" required />
                        </div>
                        <div>
                            <x-input-label for="condition" :value="__('Condition')" class="text-pink-400" />
                            <select id="condition" name="condition"
                                class="block mt-1 w-full border-gray-600 bg-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach (['new', 'excellent', 'good', 'worn'] as $cond)
                                    <option value="{{ $cond }}" @selected(old('condition', $costume->condition) === $cond)>{{ ucfirst($cond) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tags and Image --}}
                    <div>
                        <x-input-label for="tags" :value="__('Tags (Comma Separated)')"
                            class="text-pink-400" />
                        {{-- Tags are stored as JSON array, convert back to string for editing --}}
                        <x-text-input id="tags" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                            type="text" name="tags" placeholder="e.g., Fantasy, Armor, Event Promo" 
                            value="{{ old('tags', is_array($costume->tags) ? implode(', ', $costume->tags) : $costume->tags) }}" required />
                    </div>
                    
                    {{-- Image Replacement --}}
                    <div class="border-t border-gray-700 pt-4">
                        <x-input-label for="main_image" :value="__('Replace Main Costume Image')"
                            class="text-pink-400" />
                        <div class="flex items-center space-x-4 mt-2">
                            <div class="w-24 h-24 bg-gray-700 overflow-hidden rounded-lg flex-shrink-0">
                                @php
                                    $imageUrl = $costume->main_image_path ? asset('storage/' . $costume->main_image_path) : asset('default_images/default_costume.png');
                                @endphp
                                <img src="{{ $imageUrl }}" alt="Current Image" class="w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">
                            </div>
                            <input id="main_image"
                                class="block w-full text-sm text-gray-200 border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none"
                                type="file" name="main_image" />
                        </div>
                    </div>
                    

                    <div class="flex items-center justify-end">
                        <x-primary-button class="bg-pink-600 hover:bg-pink-700">
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>