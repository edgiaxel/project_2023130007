<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Discount & Sale Management 💸
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. GLOBAL FLASH SALE CONTROL --}}
            <div class="bg-gray-800 rounded-lg shadow-xl p-6 border-t-4 border-yellow-500">
                <h3 class="text-2xl font-bold text-yellow-400 mb-4">Global Flash Sale Control</h3>
                
                @if (session('status'))
                    <div class="mb-4 text-sm font-medium text-green-400 p-3 bg-green-900/50 rounded-lg">
                        {{ session('status') }}
                    </div>
                @endif
                
                <p class="text-gray-400 mb-3">This rate is **ADDITIVE** to individual costume discounts (e.g., Global 15% + Costume 5% = 20% Total).</p>
                <form action="{{ route('admin.discounts.global.set') }}" method="POST"
                    class="flex items-center space-x-4">
                    @csrf
                    <label for="global_rate" class="text-white">Global Rate (%)</label>
                    <input type="number" step="0.01" name="rate" id="global_rate"
                        value="{{ old('rate', $globalDiscount->rate * 100) }}"
                        class="bg-gray-700 border-gray-600 text-white rounded-md w-24">
                    <label class="text-white">
                        <input type="checkbox" name="is_active" {{ $globalDiscount->is_active ? 'checked' : '' }}
                            class="rounded text-yellow-500 bg-gray-700 border-gray-600">
                        Active
                    </label>
                    <button type="submit"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-md">Save Global Sale</button>
                </form>
                <p class="text-sm text-gray-500 mt-2">Current Global Discount: **{{ $globalDiscount->rate * 100 }}%**
                    ({{ $globalDiscount->is_active ? 'ACTIVE' : 'INACTIVE' }})</p>
            </div>

            {{-- 2. INDIVIDUAL COSTUME DISCOUNT MONITORING --}}
            <div class="bg-gray-800 rounded-lg shadow-xl p-6 border-t-4 border-red-500">
                <h3 class="text-2xl font-bold text-red-400 mb-6">Individual Costume Discount Monitor</h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Costume</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Renter Store</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Renter Discount (%)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">Star Lord Helmet</td>
                                <td class="px-6 py-4 whitespace-nowrap">Cosmic Threads</td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-400 font-bold">5%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-indigo-400 hover:text-indigo-600">Edit Discount</a>
                                    <a href="#" class="text-red-400 hover:text-red-600">Remove</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">Naruto Hokage Outfit</td>
                                <td class="px-6 py-4 whitespace-nowrap">Weeb Central</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-400">0%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="#" class="text-indigo-400 hover:text-indigo-600">Add Discount</a>
                                    <a href="#" class="text-red-400 hover:text-red-600 opacity-50 cursor-not-allowed">Remove</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>