@php
    use App\Models\User;
    use App\Models\Costume;
    use Illuminate\Support\Facades\Route;

    $renterId = Route::current()->parameter('user_id') ?? 1; 
    $renterUser = User::with('store')->find($renterId);
    $store = $renterUser->store ?? null;
    $costumes = $renterUser ? $renterUser->costumes()->where('is_approved', true)->get() : collect();

    $storeName = $store->store_name ?? 'Starium Shop';
    $storeDesc = $store->description ?? 'No description provided.';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                {{ $storeName }}'s Cosmic Storefront ✨
            </h2>
            {{-- BACK BUTTON --}}
            <a href="javascript:history.back()"
                class="text-sm text-indigo-400 hover:text-white transition duration-300">
                &larr; Back to Catalog
            </a>
        </div>
    </x-slot>
    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. PUBLIC STORE PROFILE HEADER --}}
            <div class="bg-gray-800 p-8 rounded-lg shadow-xl mb-10 border-l-4 border-pink-500">
                <div class="flex items-center space-x-6">
                    @php
                        $logoPath = $store->store_logo_path ?? null;
                        $logoUrl = $logoPath
                            ? asset('storage/' . $logoPath)
                            : asset('default_images/default_avatar.png'); 

                        $fallbackInitial = strtoupper(substr($storeName, 0, 1));
                    @endphp

                    <img src="{{ $logoUrl }}" alt="{{ $storeName }} Logo"
                        class="w-20 h-20 rounded-full object-cover border-4 border-pink-500"
                        onerror="this.onerror=null; this.src='{{ asset('default_images/default_avatar.png') }}';">

                    {{-- END FIXED LOGO/AVATAR DISPLAY --}}
                    <div>
                        <h1 class="text-3xl font-extrabold text-pink-400">{{ $storeName }}</h1>
                        <p class="text-gray-300 mt-1">{{ $storeDesc }}</p>
                        <p class="text-sm text-gray-500 mt-2">
                            📍 Base Address: {{ $renterUser->address ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>



            {{-- 2. STORE COSTUMES LIST --}}
            <h3 class="text-2xl font-bold text-indigo-400 mb-6">Available Costumes ({{ $costumes->count() }})</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($costumes as $costume)
                    {{-- DYNAMIC COSTUME CARD --}}
                    <div
                        class="bg-gray-800 border border-indigo-700 shadow-xl rounded-lg overflow-hidden transition transform hover:scale-[1.02] duration-300">

                        @php
                            $costumeImageUrl = $costume->main_image_path
                                ? asset('storage/' . $costume->main_image_path)
                                : asset('default_images/default_costume.png');
                        @endphp

                        <div class="h-48 overflow-hidden">
                            <img src="{{ $costumeImageUrl }}" alt="{{ $costume->name }} costume"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">
                        </div>

                        <div class="p-4">
                            <h3 class="text-xl font-bold text-white truncate">{{ $costume->name }}</h3>
                            <p class="text-gray-400 text-sm">Series: {{ $costume->series }}</p>
                            <p class="text-lg font-semibold text-indigo-400 mt-2">Rp
                                {{ number_format($costume->price_per_day, 0, ',', '.') }} / Day
                            </p>

                            <a href="{{ route('costume.detail', ['id' => $costume->id]) }}"
                                class="mt-3 block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded">
                                View & Rent
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="col-span-4 text-center text-gray-400 text-lg py-10">This renter has no active costumes yet.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>