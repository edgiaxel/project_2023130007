<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            My Cosmic Wishlist ❤️ <span class="text-pink-400">Saved for Later</span>
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($favorites->isEmpty())
                <div class="bg-gray-800 p-10 rounded-lg text-center border border-dashed border-gray-600">
                    <p class="text-gray-400 text-xl">Your wishlist is empty. Explore the catalog to find your next look!</p>
                    <a href="{{ route('catalog') }}" class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">Go to Catalog</a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($favorites as $costume)
                        <div class="bg-gray-800 border border-pink-900/50 shadow-xl rounded-lg overflow-hidden transition transform hover:scale-[1.02] relative group">
                            
                            {{-- REMOVE BUTTON OVERLAY --}}
                            <form action="{{ route('favorites.toggle', $costume->id) }}" method="POST" class="absolute top-2 right-2 z-20">
                                @csrf
                                <button type="submit" class="bg-red-600/80 hover:bg-red-600 text-white p-2 rounded-full shadow-lg transition" title="Remove from favorites">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>

                            @php
                                $mainImage = $costume->images->sortBy('order')->first();
                                $costumeImageUrl = $mainImage ? asset('storage/' . $mainImage->image_path) : asset('default_images/default_costume.png');
                            @endphp

                            <div class="h-48 overflow-hidden">
                                <img src="{{ $costumeImageUrl }}" class="w-full h-full object-cover">
                            </div>

                            <div class="p-4">
                                <h3 class="text-lg font-bold text-white truncate">{{ $costume->name }}</h3>
                                <p class="text-gray-400 text-xs mb-2">{{ $costume->series }}</p>
                                
                                <p class="text-indigo-400 font-bold">
                                    Rp {{ number_format($costume->final_price, 0, ',', '.') }} / Day
                                </p>

                                <div class="mt-4 grid grid-cols-1 gap-2">
                                    <a href="{{ route('costume.detail', $costume->id) }}" class="text-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2 rounded transition">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>