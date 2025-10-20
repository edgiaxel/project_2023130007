@php
    use App\Models\Costume;

    $costume = $costume ?? Costume::find(request()->route('costume_id'));

    $price = $costume->price_per_day ?? 60000;
    $costumeName = $costume->name ?? 'Costume Not Found';
    $costumeId = $costume->id ?? 0;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Place Order: Confirm Rental 🛒
        </h2>
    </x-slot>

    {{-- Alpine.js for live price calculation --}}
    <div class="py-12" style="background-color: #0d0d1f;" x-data="{
            startDate: '',
            endDate: '',
            pricePerDay: {{ $price }},
            duration: 0,
            totalPrice: 0,

            calculatePrice() {
                if (this.startDate && this.endDate) {
                    const start = new Date(this.startDate);
                    const end = new Date(this.endDate);
                    const diffTime = Math.abs(end - start);
                    // +1 because the rental includes the start day
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                    if (diffDays > 0) {
                        this.duration = diffDays;
                        this.totalPrice = this.pricePerDay * diffDays;
                    } else {
                        this.duration = 0;
                        this.totalPrice = 0;
                    }
                } else {
                    this.duration = 0;
                    this.totalPrice = 0;
                }
            },

            formatRupiah(number) {
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if ($errors->has('stock_error'))
            <div class="bg-red-800 p-4 rounded-lg mb-6 border border-red-500 text-white font-bold">
                <p>🚨 Renting Failed!</p>
                <p class="text-sm mt-1">{{ $errors->first('stock_error') }}</p>
            </div>
        @endif
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-indigo-500">
                <h3 class="text-3xl font-bold text-white mb-4">
                    Renting: {{ $costumeName }}
                </h3>

                <p class="text-lg text-gray-300 mb-6">
                    Price: Rp {{ number_format($price, 0, ',', '.') }} / Day
                </p>

                {{-- Booking form --}}
                <form action="{{ route('order.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Costume ID --}}
                    <input type="hidden" name="costume_id" value="{{ $costumeId }}">

                    {{-- Start Date --}}
                    <div>
                        <x-input-label for="start_date" :value="__('Rental Start Date')" class="text-indigo-400" />
                        <x-text-input id="start_date" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                            type="date" name="start_date" required x-model="startDate" x-on:change="calculatePrice" />
                    </div>

                    {{-- End Date --}}
                    <div>
                        <x-input-label for="end_date" :value="__('Rental End Date')" class="text-indigo-400" />
                        <x-text-input id="end_date" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                            type="date" name="end_date" required x-model="endDate" x-on:change="calculatePrice" />
                    </div>

                    {{-- Live Summary --}}
                    <div class="p-4 bg-gray-700 rounded-lg text-gray-200 border-l-4 border-green-500">
                        <h4 class="font-bold text-xl mb-2 text-green-400">
                            Order Summary (Live Calculation)
                        </h4>

                        <div class="flex justify-between">
                            <p>Duration:</p>
                            <p class="font-bold" x-text="duration > 0 ? duration + ' Days' : 'Select Dates'"></p>
                        </div>

                        <div class="flex justify-between">
                            <p>
                                Rental Cost (<span x-text="duration">0</span> days @
                                Rp {{ number_format($price, 0, ',', '.') }}/day):
                            </p>
                            <p x-text="formatRupiah(totalPrice)"></p>
                        </div>

                        <div class="flex justify-between font-bold text-2xl mt-3 text-green-400">
                            <p>Grand Total:</p>
                            <p x-text="formatRupiah(totalPrice)"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700" x-bind:disabled="duration <= 0">
                            {{ __('Place Booking & Await Confirmation') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>