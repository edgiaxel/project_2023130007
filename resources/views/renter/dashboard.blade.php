<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Renter Store Panel ✨ <span class="text-pink-400">Your Cosmic Catalog</span>
        </h2>
    </x-slot>

    <div class="py-12" style="background-image: url('{{ asset('images/nebula.jpg') }}');">
        {{-- Another theme placeholder --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 bg-opacity-90 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="p-6 text-gray-100 space-y-4">
                    <p class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}. Ready to sell some space
                        threads?</p>
                    <p class="text-md text-gray-400">Current Sales: Active Orders: 3 | Total Listings: 8 | Earnings: Rp
                        500.000</p>

                    <div class="mt-8 grid grid-cols-3 gap-6 text-center">
                        <a href="{{ route('renter.profile.setup') }}"
                            class="block p-6 bg-pink-700 hover:bg-pink-600 rounded-lg shadow-lg transition duration-300">
                            <span class="text-3xl font-extrabold text-white">Setup Profile</span>
                        </a>
                        <a href="{{ route('renter.costumes.upload') }}"
                            class="block p-6 bg-indigo-700 hover:bg-indigo-600 rounded-lg shadow-lg transition duration-300">
                            <span class="text-3xl font-extrabold text-white">Upload Costume</span>
                        </a>
                        <a href="{{ route('renter.orders') }}"
                            class="block p-6 bg-yellow-700 hover:bg-yellow-600 rounded-lg shadow-lg transition duration-300">
                            <span class="text-3xl font-extrabold text-white">View Orders</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>