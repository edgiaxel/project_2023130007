<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Manage All Renter Stores 🏪
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-indigo-500">
                <h3 class="text-2xl font-bold text-indigo-400 mb-6">Active Stores List ({{ $renters->count() }} Renters)</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Store Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Owner Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Listings Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Date Joined</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            @forelse ($renters as $renter)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-pink-400">{{ $renter->store->store_name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $renter->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ $renter->costumes_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $renter->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.stores.view', $renter->id) }}" class="text-indigo-400 hover:text-indigo-600">View/Edit Store</a>
                                        <a href="#" class="text-red-400 hover:text-red-600">Deactivate/Delete</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-400">No renters registered on the platform.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>