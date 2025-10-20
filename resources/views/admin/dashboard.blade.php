<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin Control Deck 🚀 <span class="text-red-400">GLOBAL OVERVIEW</span>
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            <div class="grid grid-cols-4 gap-6 text-center">
                <div class="p-6 bg-gray-800 rounded-lg shadow-xl border-t-4 border-indigo-500">
                    <p class="text-sm text-gray-400">Total Revenue (Completed)</p>
                    <p class="text-2xl font-extrabold text-green-400 mt-2">Rp
                        {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="p-6 bg-gray-800 rounded-lg shadow-xl border-t-4 border-red-500">
                    <p class="text-sm text-gray-400">Total Renters</p>
                    <p class="text-2xl font-extrabold text-white mt-2">{{ $totalRenters }}</p>
                </div>
                <div class="p-6 bg-gray-800 rounded-lg shadow-xl border-t-4 border-yellow-500">
                    <p class="text-sm text-gray-400">Costumes Pending Approval</p>
                    <p class="text-2xl font-extrabold text-white mt-2">{{ $pendingApprovals }}</p>
                </div>
                <div class="p-6 bg-gray-800 rounded-lg shadow-xl border-t-4 border-pink-500">
                    <p class="text-sm text-gray-400">Total Catalog Items</p>
                    <p class="text-2xl font-extrabold text-white mt-2">{{ $totalCostumes }}</p>
                </div>
            </div>

            {{-- ADMIN ACTION LINKS --}}
            <div class="grid grid-cols-3 gap-6 text-center">
                <a href="{{ route('admin.costumes.approval') }}"
                    class="block p-4 bg-yellow-700 hover:bg-yellow-600 rounded-lg shadow-lg transition duration-300">
                    <span class="text-xl font-extrabold text-white">Costume Approvals</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="block p-4 bg-indigo-700 hover:bg-indigo-600 rounded-lg shadow-lg transition duration-300">
                    <span class="text-xl font-extrabold text-white">Manage Users</span>
                </a> 
                <a href="{{ route('admin.transactions') }}"
                    class="block p-4 bg-pink-700 hover:bg-indigo-600 rounded-lg shadow-lg transition duration-300">
                    <span class="text-xl font-extrabold text-white">Manage Transaction</span>
                </a>
            </div>
            <div class="grid grid-cols-3 gap-6 text-center">
            <a href="{{ route('admin.discounts.manage') }}"
                    class="block p-4 bg-red-700 hover:bg-red-600 rounded-lg shadow-lg transition duration-300">
                    <span class="text-xl font-extrabold text-white">Manage Discounts</span>
                </a>
                <a href="{{ route('admin.banners.manage') }}"
                    class="block p-4 bg-blue-700 hover:bg-green-600 rounded-lg shadow-lg transition duration-300">
                    <span class="text-xl font-extrabold text-white">Edit Catalog Banners</span>
                </a>
                <a href="{{ route('admin.stores.manage') }}"
                    class="block p-4 bg-purple-700 hover:bg-green-600 rounded-lg shadow-lg transition duration-300">
                    <span class="text-xl font-extrabold text-white">Edit Renter Stores</span>
                </a>
            </div>

            {{-- RENTER SALES RANKING --}}
            <div class="bg-gray-800 rounded-lg shadow-xl p-6">
                <h3 class="text-2xl font-bold text-red-400 mb-6">Top Renters by Total Revenue</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Renter</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Total
                                    Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Costumes
                                    Listed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Top Costume
                                    Example</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            @forelse ($renterSummaries as $renter)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-white">{{ $renter->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-green-400 font-bold">Rp
                                        {{ number_format($renter->revenue, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ $renter->costumes_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $renter->top_costume }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.renters.analytics', $renter->id) }}"
                                            class="text-indigo-400 hover:text-indigo-600">View Analytics</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-400">No renter data available.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>