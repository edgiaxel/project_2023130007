<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Cosmic Catalog 🌌 <span class="text-indigo-400">Find Your Perfect Role!</span>
        </h2>

        {{-- Search Bar (Fixed at top for filter) --}}
        <div class="mt-4 flex justify-between items-center">
            <form method="GET" action="{{ route('catalog') }}" class="flex w-full max-w-lg mr-4">
                <input type="text" name="search" placeholder="Search by character, series, or any tag..."
                    class="bg-gray-700 border-indigo-500 rounded-l-md text-white px-3 py-2 w-full"
                    value="{{ request('search') }}">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-r-md">
                    Search
                </button>
                @if (request('search'))
                    <a href="{{ route('catalog') }}"
                        class="ml-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md">
                        Reset
                    </a>
                @endif
            </form>
        </div>
    </x-slot>

    {{-- Main Content Starts --}}
    <div class="bg-gray-900 min-h-screen">
        <section id="sale-banner" class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
            <h3 class="text-2xl font-bold text-yellow-400 mb-4">🚀 HIGH QUALITY RENTAL COSTUMES!</h3>

            <div x-data="{ current: 1, total: {{ $banners->count() }} }"
                x-init="setInterval(() => { current = (current % total) + 1 }, 5000)"
                class="relative overflow-hidden rounded-lg shadow-2xl">

                {{-- SLIDES CONTAINER --}}
                <div class="relative h-48">
                    @forelse ($banners as $banner)
                        @php
                            $imageUrl = $banner->image_path ? asset('storage/' . $banner->image_path) : asset('default_images/default_costume.png');
                        @endphp
                        <div x-show="current === {{ $banner->order }}" x-transition:enter.opacity x-transition:leave.opacity
                            class="absolute inset-0 w-full h-full bg-cover bg-center flex items-center justify-center text-4xl text-white font-extrabold transition duration-700"
                            style="background-image: url('{{ $imageUrl }}'); background-color: #2D3748; background-size: cover; text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">
                            {{ $banner->title }}
                        </div>
                    @empty
                        <div
                            class="absolute inset-0 w-full h-full bg-gray-600 flex items-center justify-center text-xl text-white">
                            No Banners Set. Admin Fix This!</div>
                    @endforelse
                </div>

                {{-- Navigation Arrows --}}
                <button @click="current = current > 1 ? current - 1 : total"
                    class="absolute top-1/2 left-4 transform -translate-y-1/2 p-3 bg-black/50 hover:bg-black/80 text-white rounded-full transition z-20">
                    &lt;
                </button>
                <button @click="current = current < total ? current + 1 : 1"
                    class="absolute top-1/2 right-4 transform -translate-y-1/2 p-3 bg-black/50 hover:bg-black/80 text-white rounded-full transition z-20">
                    &gt;
                </button>
            </div>
        </section>

        {{-- CATEGORIZED CATALOGS SECTION --}}
        <section id="collections" class="py-12" style="background-color: #0d0d1f;">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h3 class="text-3xl font-extrabold text-indigo-400 mb-8 border-b border-indigo-700 pb-2">📚 Featured
                    Collections</h3>

                @if (request('search'))
                    <h4 class="text-2xl font-bold text-gray-300 mb-6">Search Results for: "{{ request('search') }}"</h4>
                @endif

                @foreach ($MEDIA_CATEGORIES as $category)
                    @php
                        $categoryItems = $groupedCostumes[$category] ?? collect();
                    @endphp

                    @if ($categoryItems->isNotEmpty() && !request('search'))
                        <h4 class="text-2xl font-bold text-white mb-4 mt-8 pt-4 border-t border-gray-700">{{ $category }}
                            Costumes ({{ $categoryItems->count() }} items)</h4>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
                        @forelse ($categoryItems->take(4) as $costume)
                            {{-- DYNAMIC COSTUME CARD --}}
                            <div
                                class="bg-gray-800 border border-indigo-700 shadow-xl rounded-lg overflow-hidden transition transform hover:scale-[1.02] duration-300 relative">

                                {{-- SALE BADGE --}}
                                @if ($costume->is_on_sale)
                                    <span
                                        class="absolute top-0 right-0 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg z-10">SALE!</span>
                                @endif

                                {{-- IMAGE DISPLAY & FALLBACK LOGIC --}}
                                @php
                                    // Use the first image in the collection (order 0)
                                    $mainImage = $costume->images->sortBy('order')->first();
                                    $costumeImageUrl = $mainImage
                                        ? asset('storage/' . $mainImage->image_path)
                                        : asset('default_images/default_costume.png');
                                @endphp

                                <div class="h-48 overflow-hidden">
                                    <img src="{{ $costumeImageUrl }}" alt="{{ $costume->name }} costume"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">
                                </div>


                                <div class="p-4">
                                    {{-- Title and Series --}}
                                    <h3 class="text-xl font-bold text-white truncate">{{ $costume->name }}</h3>
                                    <p class="text-gray-400 text-sm mb-1">Series: {{ $costume->series }}</p>

                                    {{-- RENTER LINK (STORE NAME & AVATAR) --}}
                                    <p class="text-gray-400 text-xs border-b border-gray-700 pb-2">
                                        Renter:
                                        <a href="{{ route('public.store', ['user_id' => $costume->user_id]) }}"
                                            class="text-pink-400 hover:text-pink-300 font-semibold flex items-center">

                                            @php
                                                $renter = $costume->renter;
                                                $storeName = $renter->store->store_name ?? $renter->name . ' Shop';
                                                $logoPath = $renter->store->store_logo_path ?? $renter->profile_picture;
                                                $avatarUrl = $logoPath
                                                    ? asset('storage/' . $logoPath)
                                                    : asset('default_images/default_avatar.png');
                                            @endphp

                                            <img src="{{ $avatarUrl }}" alt="{{ $storeName }} Logo"
                                                class="h-4 w-4 rounded-full object-cover mr-1"
                                                onerror="this.onerror=null; this.src='{{ asset('default_images/default_avatar.png') }}';">

                                            {{ $storeName }}
                                        </a>
                                    </p>

                                    {{-- PRICE DISPLAY (WITH DISCOUNT) --}}
                                    <p class="text-lg font-semibold text-green-400 mt-2 flex flex-col">
                                        @if ($costume->is_on_sale)
                                            <span class="text-xs text-gray-500 line-through">
                                                {{-- 💥 FIX: Use price_per_day for the line-through original price --}}
                                                Rp {{ number_format($costume->price_per_day, 0, ',', '.') }} / Day
                                            </span>
                                            <span class="text-xl font-extrabold text-indigo-400">
                                                {{-- 💥 FIX: Use final_price (the calculated discounted price) --}}
                                                Rp {{ number_format($costume->final_price, 0, ',', '.') }} / Day
                                            </span>
                                        @else
                                            <span class="text-xl font-extrabold text-indigo-400">
                                                Rp {{ number_format($costume->price_per_day, 0, ',', '.') }} / Day
                                            </span>
                                        @endif
                                    </p>

                                    {{-- Link to Costume Detail Page --}}
                                    <a href="{{ route('costume.detail', ['id' => $costume->id]) }}"
                                        class="mt-3 block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded">
                                        View & Rent Now!
                                    </a>
                                </div>
                            </div>
                        @empty
                            @if (!request('search'))
                                <p class="col-span-4 text-center text-gray-400 text-lg py-10">No cosmic threads categorized as
                                    {{ $category }} yet.
                                </p>
                            @endif
                        @endforelse
                    </div>

                    @if (request('search'))
                        @break
                    @endif
                @endforeach

                {{-- Fallback for empty search results --}}
                @if (request('search') && $costumesPaginated->isEmpty())
                    <p class="col-span-4 text-center text-gray-400 text-lg py-10">No search results found for
                        "{{ request('search') }}" in the entire galaxy.</p>
                @endif
            </div>
        </section>

        {{-- ALL PRODUCTS SECTION --}}
        <section id="all-products" class="py-12" style="background-color: #1a0046;">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h3 class="text-3xl font-extrabold text-yellow-400 mb-8 border-b border-yellow-700 pb-2">📦 All
                    Available Costumes</h3>

                {{-- PER PAGE DROPDOWN MOVED HERE --}}
                <div class="mb-6 flex justify-end">
                    <form method="GET" action="{{ route('catalog') }}#all-products"
                        class="flex items-center space-x-2 text-gray-400">
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <label for="per_page" class="text-sm">Show:</label>
                        <select name="per_page" id="per_page" onchange="this.form.submit()"
                            class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-2 px-3">
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" @selected($option == $perPage)>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-sm">per page</span>
                    </form>
                </div>

                {{-- LIST ALL PAGINATED COSTUMES HERE --}}
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse ($costumesPaginated as $costume)
                        {{-- DYNAMIC COSTUME CARD (Copy-pasted from above for consistency) --}}
                        <div
                            class="bg-gray-800 border border-indigo-700 shadow-xl rounded-lg overflow-hidden transition transform hover:scale-[1.02] duration-300 relative">

                            {{-- SALE BADGE --}}
                            @if ($costume->is_on_sale)
                                <span
                                    class="absolute top-0 right-0 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg z-10">SALE!</span>
                            @endif

                            {{-- IMAGE DISPLAY & FALLBACK LOGIC --}}
                            @php
                                // Use the first image in the collection (order 0)
                                $mainImage = $costume->images->sortBy('order')->first();
                                $costumeImageUrl = $mainImage
                                    ? asset('storage/' . $mainImage->image_path)
                                    : asset('default_images/default_costume.png');
                            @endphp

                            <div class="h-48 overflow-hidden">
                                <img src="{{ $costumeImageUrl }}" alt="{{ $costume->name }} costume"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">
                            </div>


                            <div class="p-4">
                                {{-- Title and Series --}}
                                <h3 class="text-xl font-bold text-white truncate">{{ $costume->name }}</h3>
                                <p class="text-gray-400 text-sm mb-1">Series: {{ $costume->series }}</p>

                                {{-- RENTER LINK (STORE NAME & AVATAR) --}}
                                <p class="text-gray-400 text-xs border-b border-gray-700 pb-2">
                                    Renter:
                                    <a href="{{ route('public.store', ['user_id' => $costume->user_id]) }}"
                                        class="text-pink-400 hover:text-pink-300 font-semibold flex items-center">

                                        @php
                                            $renter = $costume->renter;
                                            $storeName = $renter->store->store_name ?? $renter->name . ' Shop';
                                            $logoPath = $renter->store->store_logo_path ?? $renter->profile_picture;
                                            $avatarUrl = $logoPath
                                                ? asset('storage/' . $logoPath)
                                                : asset('default_images/default_avatar.png');
                                        @endphp

                                        <img src="{{ $avatarUrl }}" alt="{{ $storeName }} Logo"
                                            class="h-4 w-4 rounded-full object-cover mr-1"
                                            onerror="this.onerror=null; this.src='{{ asset('default_images/default_avatar.png') }}';">

                                        {{ $storeName }}
                                    </a>
                                </p>

                                {{-- PRICE DISPLAY (WITH DISCOUNT) --}}
                                <p class="text-lg font-semibold text-green-400 mt-2 flex flex-col">
                                    @if ($costume->is_on_sale)
                                        <span class="text-xs text-gray-500 line-through">
                                            {{-- 💥 FIX: Use price_per_day for the line-through original price --}}
                                            Rp {{ number_format($costume->price_per_day, 0, ',', '.') }} / Day
                                        </span>
                                        <span class="text-xl font-extrabold text-indigo-400">
                                            {{-- 💥 FIX: Use final_price (the calculated discounted price) --}}
                                            Rp {{ number_format($costume->final_price, 0, ',', '.') }} / Day
                                        </span>
                                    @else
                                        <span class="text-xl font-extrabold text-indigo-400">
                                            Rp {{ number_format($costume->price_per_day, 0, ',', '.') }} / Day
                                        </span>
                                    @endif
                                </p>

                                {{-- Link to Costume Detail Page --}}
                                <a href="{{ route('costume.detail', ['id' => $costume->id]) }}"
                                    class="mt-3 block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded">
                                    View & Rent Now!
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-4 text-center text-gray-400 text-lg py-10">No items available in the complete
                            catalog.</p>
                    @endforelse
                </div>

                {{-- Pagination Links for All Products --}}
                @if ($costumesPaginated->hasPages())
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6 pb-3">
                        {{-- 💥 FIX: Append the fragment anchor to links --}}
                        {{ $costumesPaginated->fragment('all-products')->links() }}
                    </div>
                @endif
            </div>
        </section>

        {{-- ABOUT SECTION --}}
        <section id="about" class="py-12" style="background-color: #0d0d1f;">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-300">
                <h3 class="text-3xl font-extrabold text-pink-400 mb-6 border-b border-pink-700 pb-2">🌌 About Starium
                    Cosplay Rental</h3>
                <p class="mb-4 text-lg">
                    Starium Cosplay Rental is the galaxy's leading platform connecting aspiring cosplayers with top-tier
                    costume providers. Launched by Edgi Axel Limandibrata from STMIK LIKMI in October 2025, our mission
                    is to make event-ready costumes accessible across the cosmos. We ensure secure, hassle-free rentals
                    managed by our dedicated team and protected.
                </p>
                <p>
                    Whether you need armor for a convention, a historical dress for a photoshoot, or just a T-Rex
                    Kigurumi for laughs, Starium manages the catalog, inventory, and transactions, giving costume
                    providers a powerful system to manage their businesses.</p>
            </div>
        </section>

        {{-- FOOTER SECTION --}}
        @include('layouts.footer')
    </div>
</x-app-layout>