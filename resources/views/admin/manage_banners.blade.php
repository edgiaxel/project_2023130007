<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Manage Catalog Flash Sale Banners 🖼️
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="p-6 bg-gray-800 rounded-lg shadow-xl border-t-4 border-red-500">
                <h3 class="text-2xl font-bold text-red-400 mb-6">3 Catalog Banners (Order is fixed)</h3>

                <div class="space-y-8">
                    @forelse ($banners as $banner)
                        <div class="p-6 bg-gray-700 rounded-lg border-l-4 border-indigo-500">
                            <h4 class="text-xl font-bold text-white mb-4">Banner #{{ $banner->order }}</h4>
                            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                
                                {{-- Current Image Display --}}
                                <div class="mb-4">
                                    <p class="text-sm text-gray-400 mb-1">Current Image:</p>
                                    <div class="w-full h-full bg-gray-600 overflow-hidden rounded-md flex items-center justify-center">
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
                                    <x-input-label for="title_{{ $banner->id }}" :value="__('Banner Title/Text')" class="text-white" />
                                    <x-text-input id="title_{{ $banner->id }}" name="title" type="text"
                                        class="mt-1 block w-full bg-gray-600 border-gray-500 text-white" 
                                        value="{{ old('title', $banner->title) }}" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                </div>

                                {{-- Image Upload --}}
                                <div>
                                    <x-input-label for="image_file_{{ $banner->id }}" :value="__('Replace Image (JPG/PNG)')" class="text-white" />
                                    <input id="image_file_{{ $banner->id }}" name="image_file" type="file"
                                        class="block mt-1 w-full text-sm text-gray-200 border-gray-500 rounded-lg cursor-pointer bg-gray-600 focus:outline-none" />
                                    <x-input-error class="mt-2" :messages="$errors->get('image_file')" />
                                </div>

                                <div class="flex justify-end">
                                    <x-primary-button class="bg-pink-600 hover:bg-pink-700">
                                        {{ __('Update Banner') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-400">No banners initialized yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>