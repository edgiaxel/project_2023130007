@php
    use App\Models\Costume;
    use Illuminate\Support\Facades\Route;

    $costumeId = Route::current()->parameter('id');

    $costume = Costume::with(['renter.store', 'images'])->find($costumeId);
    $images = $costume->images ?? collect();


    if (!$costume) {
        $costume = (object) [
            'name' => 'Costume Not Found',
            'series' => 'N/A',
            'size' => 'N/A',
            'condition' => 'N/A',
            'price_per_day' => 0,
            'stock' => 0,
            'tags' => ['Error'],
            'is_on_sale' => false,
            'discounted_price' => 0,
            'original_price' => 0,
        ];
    }
    $isApproved = $costume && $costume->status === 'approved'; // 💥 NEW LINE
    $statusText = $costume->status ?? 'unknown'; // 💥 NEW LINE
    $tags = $costume->tags ?? ['Error'];
    $costumeName = $costume->name;
    $renterStoreName = $costume->renter->store->store_name ?? 'Unknown Store';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                Costume Detail: {{ $costumeName }} 🌙
            </h2>
        </div>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="grid grid-cols-1 lg:grid-cols-3 gap-8 bg-gray-800 border border-indigo-700 p-6 rounded-lg shadow-xl">
                {{-- Image Gallery (Multi-Image Support) --}}
                {{-- Image Gallery (Multi-Image Slider) --}}
                <div class="lg:col-span-2" x-data="{ currentImage: 1, totalImages: {{ $images->count() }}, 
                                    images: {{ json_encode($images->sortBy('order')->pluck('image_path')) }} }">

                    @php
                        // Get the sorted list of image paths for Alpine JS to use
                        $sortedImages = $images->sortBy('order')->pluck('image_path')->map(fn($path) => asset('storage/' . $path));
                    @endphp
                    {{-- Main Image Slider Area --}}
                    <div
                        class="relative w-full mb-4 bg-gray-700 rounded-lg overflow-hidden border border-indigo-500 max-h-[500px]">

                        @forelse ($sortedImages as $index => $path)
                            <img x-show="currentImage === {{ $index + 1 }}" x-transition:enter.opacity
                                x-transition:leave.opacity src="{{ $path }}" alt="{{ $costumeName }} Image {{ $index + 1 }}"
                                class="w-full object-cover h-full"
                                onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">
                        @empty
                            <img src="{{ asset('default_images/default_costume.png') }}" alt="No Image"
                                class="w-full object-cover max-h-[500px]">
                        @endforelse

                        {{-- Navigation Arrows --}}
                        @if ($images->count() > 1)
                            <button @click="currentImage = currentImage > 1 ? currentImage - 1 : totalImages"
                                class="absolute top-1/2 left-4 transform -translate-y-1/2 p-2 bg-black/50 hover:bg-black/80 text-white rounded-full transition z-20">
                                &lt;
                            </button>
                            <button @click="currentImage = currentImage < totalImages ? currentImage + 1 : 1"
                                class="absolute top-1/2 right-4 transform -translate-y-1/2 p-2 bg-black/50 hover:bg-black/80 text-white rounded-full transition z-20">
                                &gt;
                            </button>
                        @endif
                    </div>

                    {{-- Thumbnail Row (To select image) --}}
                    @if ($images->count() > 1)
                        <div class="flex space-x-3 overflow-x-auto p-2 bg-gray-700 rounded-lg">
                            @foreach ($sortedImages as $index => $path)
                                <div @click="currentImage = {{ $index + 1 }}"
                                    class="w-20 h-20 flex-shrink-0 rounded-md overflow-hidden border cursor-pointer hover:border-pink-500 transition"
                                    :class="{'border-pink-500 ring-2 ring-pink-500': currentImage === {{ $index + 1 }}, 'border-gray-600': currentImage !== {{ $index + 1 }} }">
                                    <img src="{{ $path }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover"
                                        onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Details and Order Box --}}
                <div class="lg:col-span-1 text-gray-200">
                    <h2 class="text-4xl font-bold text-green-400 mt-4 flex flex-col">
                        @if ($costume->is_on_sale)
                            <span class="text-lg font-semibold text-gray-500 line-through">
                                Rp {{ number_format($costume->price_per_day, 0, ',', '.') }} / Day
                            </span>
                            <span class="text-4xl font-extrabold text-indigo-400">
                                Rp {{ number_format($costume->final_price, 0, ',', '.') }} <span
                                    class="text-xl text-gray-400">/ Day</span>
                            </span>
                        @else
                            <span class="text-4xl font-extrabold text-indigo-400">
                                Rp {{ number_format($costume->price_per_day, 0, ',', '.') }} <span
                                    class="text-xl text-gray-400">/ Day</span>
                            </span>
                        @endif
                    </h2>

                    <div class="mt-6 p-4 bg-gray-700 rounded-lg border-l-4 border-pink-500 relative">
                        @if ($costume->is_on_sale)
                            <span
                                class="absolute top-0 right-0 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg z-10">
                                FLASH SALE!
                            </span>
                        @endif

                        <p class="text-sm">Renter: <a
                                href="{{ route('public.store', ['user_id' => $costume->user_id ?? 0]) }}"
                                class="text-pink-400 hover:underline">{{ $renterStoreName }}</a>
                        </p>
                        <p class="text-sm text-red-400 font-bold">Stock: {{ $costume->stock }} | Availability:
                            {{ $costume->stock > 0 ? 'Available Now!' : 'Out of Stock!' }}
                        </p>
                    </div>

                    <div
                        class="mt-6 p-4 bg-gray-700 rounded-lg border-l-4 @if($isApproved) border-green-500 @else border-red-500 @endif">
                        <p class="text-lg font-bold text-white mb-2">Costume Status:</p>
                        <p class="text-3xl font-extrabold @if($isApproved) text-green-400 @else text-red-400 @endif">
                            {{ strtoupper($statusText) }}
                        </p>
                    </div>

                    <div class="mt-6 space-y-3">
                        @if ($costume->stock > 0 && $isApproved)
                            <p class="text-sm text-green-400 font-bold">
                                Stock Available: {{ $costume->stock }} units!
                            </p>
                            <a href="{{ route('order.place', ['costume_id' => $costume->id]) }}"  
                                class="w-full block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg text-lg transition duration-300">
                                Rent Now!
                            </a>
                        @elseif (!$isApproved)
                            <p class="text-lg text-red-500 font-extrabold text-center border border-red-500 p-3 rounded-lg">
                                RENTING DISABLED: Costume is not yet approved or is
                                rejected.
                            </p>
                        @else
                            <p class="text-lg text-red-500 font-extrabold text-center border border-red-500 p-3 rounded-lg">
                                ALL STOCK RENTED OUT!
                            </p>
                        @endif
                    </div>

                    <div class="mt-6">
                        <form action="{{ route('favorites.toggle', $costume->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 border-2 py-3 rounded-lg font-bold transition duration-300 
                            {{ Auth::user()->favorites->contains($costume->id)
                        ? 'border-pink-500 text-pink-500 hover:bg-pink-500 hover:text-white'
                        : 'border-gray-500 text-gray-400 hover:border-pink-400 hover:text-pink-400' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                    fill="{{ Auth::user()->favorites->contains($costume->id) ? 'currentColor' : 'none' }}"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                {{ Auth::user()->favorites->contains($costume->id) ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                            </button>
                        </form>
                    </div>



                    <div class="mt-6">
                        <p class="text-sm font-semibold border-b border-gray-600 pb-1 mb-2">Tags:</p>
                        @foreach ($tags as $tag)
                            <span
                                class="inline-block bg-indigo-800 text-indigo-100 px-3 py-1 text-xs font-semibold rounded-full mr-2 mb-2">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{-- REVIEWS SECTION --}}
            <div class="mt-12 bg-gray-800 p-8 border border-indigo-700 p-6 rounded-lg shadow-xl" x-data="{ 
                        filter: 0, 
                        sort: 'newest', 
                        reviews: {{ json_encode($costume->reviews->load('user')) }},
                        get filtered() {
                            let f = this.filter == 0 ? this.reviews : this.reviews.filter(r => r.rating == this.filter);
                            return f.sort((a,b) => this.sort === 'newest' ? new Date(b.created_at) - new Date(a.created_at) : b.rating - a.rating);
                        }
                    }">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h3 class="text-3xl font-bold text-white">Guest Experiences</h3>
                        <p class="text-yellow-400 text-lg font-bold">Average: {{ $costume->average_rating }} /
                            5.0 ⭐</p>
                    </div>
                    <div class="flex gap-2">
                        <select x-model="filter" class="bg-gray-700 text-white text-xs rounded border-none">
                            <option value="0">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                        <select x-model="sort" class="bg-gray-700 text-white text-xs rounded border-none">
                            <option value="newest">Newest First</option>
                            <option value="highest">Best Rating</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="r in filtered" :key="r.id">
                        <div class="bg-gray-900 p-5 rounded-lg border-l-4 border-indigo-500 shadow-xl">
                            <div class="flex justify-between items-center mb-2">
                                <p class="font-bold text-pink-400" x-text="r.user.name"></p>
                                <p class="text-yellow-400" x-text="'★'.repeat(r.rating) + '☆'.repeat(5-r.rating)"></p>
                            </div>
                            <p class="text-gray-300 italic" x-text="r.comment"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>