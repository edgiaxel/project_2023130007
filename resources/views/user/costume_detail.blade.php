@php
    use App\Models\Costume;
    use Illuminate\Support\Facades\Route;

    $DISCOUNT_RATE = 0.15;

    $costumeId = Route::current()->parameter('id');

    $costume = Costume::with('renter.store')->find($costumeId);

    $costume->is_on_sale = true;
    $costume->original_price = $costume->price_per_day;
    $costume->discounted_price = $costume->original_price * (1 - $DISCOUNT_RATE);

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
                {{-- Image Gallery (Dummy) --}}
                <div class="lg:col-span-2">
                    @php
                        $costumeImagePath = $costume->main_image_path
                            ? asset('storage/' . $costume->main_image_path)
                            : asset('default_images/default_costume.png');
                    @endphp

                    <div
                        class="bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 text-2xl font-bold overflow-hidden">

                        <img src="{{ $costumeImagePath }}" alt="{{ $costumeName }} Costume" class="h-full object-cover"
                            onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">

                    </div>
                </div>

                {{-- Details and Order Box --}}
                <div class="lg:col-span-1 text-gray-200">
                    <h2 class="text-4xl font-bold text-green-400 mt-4 flex flex-col">
                        @if ($costume->is_on_sale)
                            <span class="text-lg font-semibold text-gray-500 line-through">
                                Rp {{ number_format($costume->original_price, 0, ',', '.') }} / Day
                            </span>
                        @endif
                        <span class="text-4xl font-extrabold text-indigo-400">
                            Rp {{ number_format($costume->discounted_price, 0, ',', '.') }} <span
                                class="text-xl text-gray-400">/ Day</span>
                        </span>
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

                    <div class="mt-6 space-y-3">
                        @if ($costume->stock > 0)
                            <p class="text-sm text-green-400 font-bold">
                                Stock Available: {{ $costume->stock }} units!
                            </p>
                            <a href="{{ route('order.place', ['costume_id' => $costume->id]) }}"
                                class="w-full block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg text-lg transition duration-300">
                                Rent Now!
                            </a>
                        @else
                            <p class="text-lg text-red-500 font-extrabold text-center border border-red-500 p-3 rounded-lg">
                                ALL STOCK RENTED OUT!
                            </p>
                        @endif
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