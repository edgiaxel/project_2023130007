<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Renter Analytics for ✨ {{ $store->store_name ?? $renter->name }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="p-6 bg-gray-800 rounded-lg shadow-xl border-t-4 border-pink-500">
                <h3 class="text-2xl font-bold text-pink-400 mb-4">Renter Summary ({{ $renter->name }})</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-4 bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-400">Total Listings</p>
                        <p class="text-xl font-bold text-white mt-1">{{ $costumes->count() }}</p>
                    </div>
                    <div class="p-4 bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-400">Total Estimated Sales</p>
                        <p class="text-xl font-bold text-white mt-1">~Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="p-4 bg-gray-700 rounded-lg">
                        <p class="text-sm text-gray-400">Store Status</p>
                        <p class="text-xl font-bold text-green-400 mt-1">Active</p>
                    </div>
                </div>
            </div>
            
            {{-- COSTUME SALES BREAKDOWN --}}
            <div class="bg-gray-800 rounded-lg shadow-xl p-6">
                <h3 class="text-2xl font-bold text-red-400 mb-6">Costume Sales Breakdown</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Costume Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Current
                                    Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Total Rented
                                    Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            @forelse ($costumes as $costume)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $costume->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ $costume->stock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ $costume->orders_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="#" class="text-indigo-400 hover:text-indigo-600">Edit Costume</a>
                                        <a href="#" class="text-red-400 hover:text-red-600">Delete</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-400">No costumes listed by this
                                        renter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>