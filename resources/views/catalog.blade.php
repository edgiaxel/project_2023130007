@php
use App\Models\Costume;
$costumes = Costume::where('is_approved', true)->limit(12)->get(); // Get up to 12 approved costumes
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Cosmic Catalog 🌌 <span class="text-indigo-400">Find Your Perfect Role!</span>
        </h2>
        {{-- Search Bar and Filters Placeholder --}}
        <div class="mt-2 flex space-x-4">
            <input type="text" placeholder="Search by character or series..."
                class="bg-gray-700 border-indigo-500 rounded-md text-white px-3 py-2 w-full max-w-sm">
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">Search</button>
        </div>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($costumes as $costume)
                {{-- DYNAMIC COSTUME CARD --}}
                <div
                    class="bg-gray-800 border border-indigo-700 shadow-xl rounded-lg overflow-hidden transition transform hover:scale-[1.02] duration-300">
                    <div class="h-48 bg-gray-700 flex items-center justify-center text-gray-400 font-bold italic">

                    </div>
                    <div class="p-4">
                        <h3 class="text-xl font-bold text-white truncate">{{ $costume->name }}</h3>
                        <p class="text-gray-400 text-sm">Series: {{ $costume->series }}</p>
                        <p class="text-lg font-semibold text-indigo-400 mt-2">Rp
                            {{ number_format($costume->price_per_day, 0, ',', '.') }} / Day</p>

                        {{-- Link to Costume Detail Page --}}
                        <a href="{{ route('costume.detail', ['id' => $costume->id]) }}"
                            class="mt-3 block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 rounded">
                            View & Rent Now!
                        </a>
                    </div>
                </div>
                @empty
                <p class="col-span-4 text-center text-gray-400 text-lg py-10">No cosmic threads available right now,
                    check back later!</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>