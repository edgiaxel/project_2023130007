@php
use App\Models\Costume;
// Hardcode a costume ID for the mockup, matching the route definition 'order/place/{costume_id}'
$costumeId = 1;
$costume = Costume::find($costumeId);
$price = $costume->price_per_day ?? 60000;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Place Order: Confirm Rental 🛒
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-indigo-500">
                <h3 class="text-3xl font-bold text-white mb-4">Renting: {{ $costume->name ?? 'T-Rex Kigurumi' }}</h3>
                <p class="text-lg text-gray-300 mb-6">Price: Rp {{ number_format($price, 0, ',', '.') }} / Day</p>

                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    {{-- Rental Dates --}}
                    <div>
                        <x-input-label for="start_date" :value="__('Rental Start Date')" class="text-indigo-400" />
                        <x-text-input id="start_date" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                            type="date" name="start_date" required />
                    </div>
                    <div>
                        <x-input-label for="end_date" :value="__('Rental End Date')" class="text-indigo-400" />
                        <x-text-input id="end_date" class="block mt-1 w-full bg-gray-700 border-gray-600 text-white"
                            type="date" name="end_date" required />
                    </div>

                    {{-- Summary and Confirmation --}}
                    <div class="p-4 bg-gray-700 rounded-lg text-gray-200 border-l-4 border-green-500">
                        <h4 class="font-bold text-xl mb-2 text-green-400">Order Summary (DUMMY CALCULATION)</h4>
                        <div class="flex justify-between">
                            <p>Duration:</p>
                            <p class="font-bold">5 Days</p> {{-- Hardcode days for mockup --}}
                        </div>
                        <div class="flex justify-between">
                            <p>Rental Cost (5 days @ Rp {{ number_format($price, 0, ',', '.') }}/day):</p>
                            <p>Rp {{ number_format($price * 5, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex justify-between font-bold text-2xl mt-3 text-green-400">
                            <p>Grand Total:</p>
                            <p>Rp {{ number_format($price * 5, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Place Booking & Await Confirmation') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>