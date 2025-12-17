<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Manage Catalog Flash Sale Banners 🖼️ <a href="{{ route('admin.soft_delete.index') }}" class="ml-4 text-sm text-red-400 hover:text-red-300 font-semibold">(View Trash Bin)</a>
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('status'))
                <div class="mb-4 text-sm font-medium text-green-400 p-3 bg-green-900/50 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->has('banner_limit'))
                <div class="mb-4 text-sm font-medium text-red-400 p-3 bg-red-900/50 rounded-lg">
                    {{ $errors->first('banner_limit') }}
                </div>
            @endif

            {{-- 1. ADD NEW BANNER FORM (Conditional Display) --}}
            @if ($banners->count() < $maxBanners)
                <div class="p-6 bg-gray-800 rounded-lg shadow-xl border-t-4 border-indigo-500">
                    <h3 class="text-2xl font-bold text-indigo-400 mb-4">
                        + Add New Banner ({{ $banners->count() }} / {{ $maxBanners }})
                    </h3>
                    <form action="{{ route('admin.banners.add') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="new_title" :value="__('Banner Title/Text')" class="text-white" />
                            <x-text-input id="new_title" name="title" type="text"
                                class="mt-1 block w-full bg-gray-600 border-gray-500 text-white" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>
                        <div>
                            <x-input-label for="new_image_file" :value="__('Upload Banner Image (Required)')"
                                class="text-white" />
                            <input id="new_image_file" name="image_file" type="file" required
                                class="block mt-1 w-full text-sm text-gray-200 border-gray-500 rounded-lg cursor-pointer bg-gray-600 focus:outline-none" />
                            <x-input-error class="mt-2" :messages="$errors->get('image_file')" />
                        </div>
                        <div class="flex justify-end">
                            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                                {{ __('Add Banner') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            @else
                <div class="p-6 bg-gray-800 rounded-lg border-t-4 border-red-500 text-center">
                    <p class="text-xl font-bold text-red-400">Maximum banner limit reached ({{ $maxBanners }}).</p>
                </div>
            @endif

            {{-- 2. EXISTING BANNER LIST (EDIT/REORDER/DELETE) --}}
            <div class="p-6 bg-gray-800 rounded-lg shadow-xl border-t-4 border-yellow-500">
                <h3 class="text-2xl font-bold text-yellow-400 mb-6">Current Banners (Manage Order & Content)</h3>

                <div class="space-y-8">
                    @forelse ($banners as $banner)
                        <div class="p-6 bg-gray-700 rounded-lg border-l-4 border-indigo-500 space-y-4">
                            <div class="flex justify-between items-start">
                                <h4 class="text-xl font-bold text-white">Banner #{{ $banner->order }}</h4>

                                {{-- REORDER & DELETE ACTIONS --}}
                                <div class="space-x-2 flex items-center">
                                    {{-- UP Button (Move Left/Up) --}}
                                    <form action="{{ route('admin.banners.swap_order', $banner->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit"
                                            class="text-sm text-indigo-400 hover:text-white disabled:opacity-50"
                                            @disabled($banner->order === 1) title="Move Up/Left">
                                            &#x25B2; Up
                                        </button>
                                    </form>

                                    {{-- DOWN Button (Move Right/Down) --}}
                                    <form action="{{ route('admin.banners.swap_order', $banner->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit"
                                            class="text-sm text-indigo-400 hover:text-white disabled:opacity-50"
                                            @disabled($banner->order === $banners->count()) title="Move Down/Right">
                                            &#x25BC; Down
                                        </button>
                                    </form>

                                    {{-- Delete Button (Must maintain minimum 1) --}}
                                    <form action="{{ route('admin.banners.delete', $banner->id) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete Banner #{{ $banner->order }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-400 hover:text-red-200"
                                            @disabled($banners->count() <= $minBanners)>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- EDIT FORM --}}
                            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST"
                                enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                {{-- Current Image Display --}}
                                {{-- ... (Image display code remains unchanged) ... --}}
                                <div class="mb-4">
                                    <p class="text-sm text-gray-400 mb-1">Current Image:</p>
                                    <div
                                        class="w-full h-full bg-gray-600 overflow-hidden rounded-md flex items-center justify-center">
                                        @php
                                            $imageUrl = $banner->image_path ? asset('storage/' . $banner->image_path) : asset('default_images/default_costume.png');
                                        @endphp
                                        <img src="{{ $imageUrl }}" alt="Banner Image {{ $banner->order }}"
                                            class="h-full w-full object-cover"
                                            onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">
                                    </div>
                                    @if ($banner->image_path)
                                        <p class="text-xs text-gray-500 mt-1">File: {{ basename($banner->image_path) }}</p>
                                    @endif
                                </div>

                                {{-- Title Input --}}
                                <div>
                                    <x-input-label for="title_{{ $banner->id }}" :value="__('Banner Title/Text')"
                                        class="text-white" />
                                    <x-text-input id="title_{{ $banner->id }}" name="title" type="text"
                                        class="mt-1 block w-full bg-gray-600 border-gray-500 text-white"
                                        value="{{ old('title', $banner->title) }}" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                </div>

                                {{-- Image Upload --}}
                                <div>
                                    <x-input-label for="image_file_{{ $banner->id }}" :value="__('Replace Image (JPG/PNG)')"
                                        class="text-white" />
                                    <input id="image_file_{{ $banner->id }}" name="image_file" type="file"
                                        class="block mt-1 w-full text-sm text-gray-200 border-gray-500 rounded-lg cursor-pointer bg-gray-600 focus:outline-none" />
                                    <x-input-error class="mt-2" :messages="$errors->get('image_file')" />
                                </div>

                                <div class="flex justify-end">
                                    <x-primary-button class="bg-pink-600 hover:bg-pink-700">
                                        {{ __('Update Banner Content') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-400">No banners initialized yet. Please add a banner above.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>