@php
use App\Models\Costume;
// NOTE: Hardcoding ID=1 for mockup purposes, as 'id' isn't automatically passed via Route::view.
$costume = Costume::find(1);
// FIX: $costume->tags is ALREADY a PHP array due to the model's $casts property. No json_decode needed.
$tags = $costume ? $costume->tags : ['Placeholder', 'No Data'];
$costumeName = $costume->name ?? 'Unknown Costume';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Costume Detail: {{ $costumeName }} 🌙
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="grid grid-cols-1 lg:grid-cols-3 gap-8 bg-gray-800 border border-indigo-700 p-6 rounded-lg shadow-xl">
                {{-- Image Gallery (Dummy) --}}
                <div class="lg:col-span-2">
                    <div
                        class="bg-gray-700 h-96 rounded-lg flex items-center justify-center text-gray-400 text-2xl font-bold">

                    </div>
                </div>

                {{-- Details and Order Box --}}
                <div class="lg:col-span-1 text-gray-200">
                    <h1 class="text-3xl font-extrabold text-indigo-400">{{ $costume->name ?? 'N/A' }}</h1>
                    <p class="text-xl mt-2">Series: **{{ $costume->series ?? 'N/A' }}**</p>
                    <p class="text-lg mt-1">Size: **{{ $costume->size ?? 'M' }}**</p>
                    <p class="text-lg">Condition: **{{ $costume->condition ?? 'Good' }}**</p>

                    <h2 class="text-4xl font-bold text-green-400 mt-4">Rp
                        {{ number_format($costume->price_per_day ?? 0, 0, ',', '.') }} <span
                            class="text-xl text-gray-400">/ Day</span></h2>

                    <div class="mt-6 p-4 bg-gray-700 rounded-lg border-l-4 border-pink-500">
                        <p class="text-sm">Renter: <a href="#"
                                class="text-pink-400 hover:underline">{{ $costume->renter->name ?? 'Unknown Renter' }}</a>
                        </p>
                        <p class="text-sm text-red-400 font-bold">Stock: {{ $costume->stock ?? 0 }} | Availability:
                            Available Now!</p>
                    </div>

                    <div class="mt-6 space-y-3">
                        {{-- LINK TO PLACE ORDER PAGE --}}
                        <a href="{{ route('order.place', ['costume_id' => $costume->id ?? 1]) }}"
                            class="w-full block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg text-lg">
                            Rent Now!
                        </a>
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
        </div>
    </div>
</x-app-layout>