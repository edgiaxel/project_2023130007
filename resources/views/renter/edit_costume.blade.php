<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Renter: Edit Cosmic Costume: <span class="text-pink-400">{{ $costume->name }}</span> 🚀
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-pink-500">
                <form action="{{ route('renter.costumes.update', $costume->id) }}" method="POST"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    {{-- Add this right below @csrf and @method('PATCH') --}}
                    @if(request()->query('redirect_to'))
                        <input type="hidden" name="redirect_to" value="{{ request()->query('redirect_to') }}">
                    @endif
                    {{-- Costume Status Indicator --}}
                    {{-- 💥 FIX: Use $costume->status for coloring and text display --}}
                    <p class="text-sm font-bold 
                        @if ($costume->status === 'approved')
                            text-green-400
                        @elseif ($costume->status === 'rejected')
                            text-red-400
                        @else
                            text-yellow-400
                        @endif
                        pb-2 border-b border-gray-700">

                        Status:
                        @if ($costume->status === 'approved')
                            LIVE in Catalog
                        @elseif ($costume->status === 'rejected')
                            REJECTED (Needs Revision)
                        @else
                            PENDING ADMIN APPROVAL
                        @endif
                    </p>

                    {{-- Costume Basic Details --}}
                    <div>
                        <x-input-label for="character_name" :value="__('Character Name')" class="text-pink-400" />
                        <x-text-input id="character_name"
                            class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="text"
                            name="character_name" value="{{ old('character_name', $costume->name) }}" required
                            autofocus />
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
                                type="number" name="price" value="{{ old('price', $costume->price_per_day) }}"
                                required />
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
                                    <option value="{{ $cond }}" @selected(old('condition', $costume->condition) === $cond)>
                                        {{ ucfirst($cond) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tags and Image --}}
                    <div>
                        <x-input-label for="tags" :value="__('Tags (Comma Separated)')" class="text-pink-400" />
                        {{-- Tags are stored as JSON array, convert back to string for editing --}}
                        <x-text-input id="tags" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                            type="text" name="tags" placeholder="e.g., Fantasy, Armor, Event Promo"
                            value="{{ old('tags', is_array($costume->tags) ? implode(', ', $costume->tags) : $costume->tags) }}"
                            required />
                    </div>

                    {{-- 💥 NEW: DISCOUNT EDIT SECTION --}}
                    <h4 class="text-xl font-bold text-yellow-400 border-b border-gray-700 pt-4 pb-2">
                        Discount Configuration
                    </h4>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="discount_value" :value="__('Discount Value')" class="text-yellow-400" />
                            <x-text-input id="discount_value"
                                class="block mt-1 w-full bg-gray-700 border-gray-600 text-white" type="number"
                                name="discount_value" value="{{ old('discount_value', $costume->discount_value ?? 0) }}"
                                min="0" />
                        </div>
                        <div>
                            <x-input-label for="discount_type" :value="__('Type')" class="text-yellow-400" />
                            <select id="discount_type" name="discount_type"
                                class="block mt-1 w-full border-gray-600 bg-gray-700 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="percentage" @selected(old('discount_type', $costume->discount_type) === 'percentage')>Percentage (%)</option>
                                <option value="fixed" @selected(old('discount_type', $costume->discount_type) === 'fixed')>Fixed Price (Rp)</option>
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

                    <div class="flex items-center justify-end border-t border-gray-700 pt-4">
                        <x-primary-button class="bg-pink-600 hover:bg-pink-700">
                            {{ __('Save Text Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- --- SECTION 2: IMAGE MANAGEMENT (Modular Approach) --- --}}

    <div class="py-12 pt-0" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-yellow-500">
                <h3 class="text-xl font-bold text-yellow-400 mb-4">🖼️ Image Management</h3>
                <x-input-error :messages="$errors->get('image_error')" class="mb-2" />
                <x-input-error :messages="$errors->get('new_images.*')" class="mb-2" />
                <x-input-error :messages="$errors->get('replacement_file')" class="mb-2" />

                <div class="space-y-6">

                    {{-- 2A. IMAGE LIST, REORDER, REPLACE, DELETE --}}
                    <div class="border border-gray-700 rounded-md p-4">
                        <p class="text-white font-semibold mb-3">Current Images (Total: {{ $costume->images->count() }})
                        </p>

                        <div class="flex space-x-3 overflow-x-auto pb-4">
                            @forelse ($costume->images->sortBy('order') as $image)
                                @php
                                    $imageUrl = asset('storage/' . $image->image_path);
                                    $isMain = $image->order === 0;
                                    $isLast = $image->order === ($costume->images->count() - 1);
                                @endphp
                                <div
                                    class="relative w-48 h-auto flex-shrink-0 bg-gray-700 rounded-lg overflow-hidden border {{ $isMain ? 'border-yellow-500' : 'border-gray-700' }}">

                                    <div class="w-full h-36 overflow-hidden">
                                        <img src="{{ $imageUrl }}" alt="Costume Image {{ $image->order + 1 }}"
                                            class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">
                                        @if ($isMain)
                                            <span
                                                class="absolute top-0 left-0 bg-yellow-500 text-black text-xs font-bold px-2 py-1 rounded-br-lg">MAIN</span>
                                        @endif
                                    </div>

                                    <div class="p-2 space-y-2 text-xs">
                                        <p class="{{ $isMain ? 'text-yellow-400' : 'text-gray-400' }}">Position:
                                            {{ $image->order + 1 }}
                                        </p>

                                        {{-- 1. REORDER CONTROLS (Dedicated small POST request) --}}
                                        <div class="flex justify-between items-center border-t border-gray-600 pt-1">
                                            <form action="{{ route('renter.costumes.images.swap', $costume->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="image_id" value="{{ $image->id }}">
                                                <input type="hidden" name="direction" value="left">
                                                <button type="submit" class="text-indigo-400 hover:text-indigo-200"
                                                    @disabled($isMain)>
                                                    &larr; Left
                                                </button>
                                            </form>
                                            <form action="{{ route('renter.costumes.images.swap', $costume->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="image_id" value="{{ $image->id }}">
                                                <input type="hidden" name="direction" value="right">
                                                <button type="submit" class="text-indigo-400 hover:text-indigo-200"
                                                    @disabled($isLast)>
                                                    Right &rarr;
                                                </button>
                                            </form>
                                        </div>

                                        {{-- 2. REPLACE IMAGE FORM (Dedicated small POST request) --}}
                                        <form action="{{ route('renter.images.replace', $image->id) }}" method="POST"
                                            enctype="multipart/form-data" class="pt-2 border-t border-gray-600">
                                            @csrf
                                            <x-input-label for="replacement_file_{{ $image->id }}"
                                                class="text-pink-400 block mb-1">Replace:</x-input-label>
                                            <input id="replacement_file_{{ $image->id }}"
                                                class="w-full text-xs text-gray-200 rounded-lg cursor-pointer bg-gray-700"
                                                type="file" name="replacement_file" required
                                                accept="image/jpeg, image/png, image/jpg" />
                                            <button type="submit"
                                                class="w-full mt-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 rounded">Replace</button>
                                        </form>

                                        {{-- 3. DELETE BUTTON (Dedicated small DELETE request) --}}
                                        @if ($costume->images->count() > 1)
                                            <form action="{{ route('renter.images.delete', $image->id) }}" method="POST"
                                                class="mt-2 text-center">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-xs w-full bg-red-600 hover:bg-red-700 text-white font-bold py-1 rounded"
                                                    onsubmit="return confirm('Are you sure you want to delete this image?');">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm">No images uploaded yet.</p>
                            @endforelse
                        </div>
                    </div>


                    {{-- 2B. ADD NEW IMAGES SECTION (Dedicated Form) --}}
                    <div class="border border-gray-700 rounded-md p-4">
                        <form action="{{ route('renter.costumes.images.add', $costume->id) }}" method="POST"
                            enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <x-input-label for="new_images" :value="__('Upload NEW Images')" class="text-yellow-400" />
                            <input id="new_images"
                                class="block w-full text-sm text-gray-200 border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none"
                                type="file" name="new_images[]" multiple required
                                accept="image/jpeg, image/png, image/jpg" />
                            <p class="text-xs text-gray-400">Select files to add to the end of your gallery (Max 5 total
                                images).</p>
                            <x-primary-button class="bg-yellow-600 hover:bg-yellow-700 w-full">
                                {{ __('Add New Images') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mt-12 bg-gray-800 p-6 rounded-lg border-t-4 border-indigo-500">
                <h3 class="text-xl font-bold text-white mb-4">Customer Feedback & Cosmic Ratings</h3>
                <div class="space-y-4">
                    @foreach($costume->reviews as $rev)
                        <div class="p-4 bg-gray-700 rounded-md border border-gray-600 shadow-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-yellow-400 font-extrabold text-lg">{{ $rev->rating }} ⭐</span>
                                    <p class="text-xs text-gray-400">by {{ $rev->user->name }} on
                                        {{ $rev->created_at->format('d M Y') }}</p>
                                </div>

                                {{-- 🛡️ ADMIN / OWNER ACTION: DIRECT DELETE --}}
                                @if(Auth::user()->hasAnyRole(['admin', 'owner']))
                                    @php $adminDelUrl = route('admin.moderation.approve', ['id' => 'REPLACE_ID']); @endphp
                                    <form action="{{ str_replace('REPLACE_ID', $rev->id, $adminDelUrl) }}" method="POST"
                                        onsubmit="return confirm('ADMIN OVERRIDE: Permanently wipe this review from the galaxy?');">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white text-[10px] font-bold py-1 px-2 rounded uppercase tracking-tighter">
                                            Admin: Delete 💥
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <p class="text-gray-200 mt-2 italic bg-gray-800 p-2 rounded">"{{ $rev->comment }}"</p>

                            {{-- 🚩 RENTER ACTION: FLAG FOR REVIEW --}}
                            @if(Auth::user()->hasRole('renter') && Auth::id() === $costume->user_id)
                                <div x-data="{ open: false, reason: '' }" class="mt-3">
                                    <button @click="open = !open" type="button"
                                        class="text-xs text-red-400 hover:text-red-300 font-semibold flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                                        </svg>
                                        Report Review
                                    </button>

                                    <form x-show="open" x-transition action="{{ route('renter.review.flag', $rev->id) }}"
                                        method="POST" class="mt-2 p-3 bg-gray-900 rounded border border-red-900/50">
                                        @csrf
                                        <label class="block text-[10px] text-gray-500 uppercase font-bold mb-1">Reason for
                                            removal request:</label>
                                        <textarea x-model="reason" name="reason"
                                            placeholder="Explain why this review is misleading or spam..."
                                            class="bg-gray-800 text-white text-xs rounded w-full border-gray-700 focus:border-red-500 min-h-[60px]"
                                            required></textarea>
                                        <div class="flex justify-end mt-2">
                                            <button type="submit" :disabled="reason.trim().length < 5"
                                                :class="reason.trim().length < 5 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-600'"
                                                class="bg-red-700 text-white text-[10px] px-3 py-1 rounded font-bold transition">
                                                Transmit Flag 🛰️
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if($costume->reviews->isEmpty())
                        <p class="text-gray-500 italic text-sm">No feedback received for this costume yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>