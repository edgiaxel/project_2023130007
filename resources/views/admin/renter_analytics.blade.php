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
            <div class="bg-gray-800 rounded-lg shadow-xl p-6" x-data="{
    search: '',
    sortColumn: 'orders_count',
    sortDirection: 'desc',
    items: {{ json_encode($costumes) }},

    get filteredItems() {
        let filtered = this.items.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()));
        return filtered.sort((a, b) => {
            let aVal = a[this.sortColumn];
            let bVal = b[this.sortColumn];
            if (['stock', 'orders_count'].includes(this.sortColumn)) {
                aVal = parseInt(aVal) || 0;
                bVal = parseInt(bVal) || 0;
            } else {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }
            const comp = aVal > bVal ? 1 : (aVal < bVal ? -1 : 0);
            return this.sortDirection === 'asc' ? comp : -comp;
        });
    },
    sort(col) {
        if(this.sortColumn === col) this.sortDirection = (this.sortDirection === 'asc' ? 'desc' : 'asc');
        else { this.sortColumn = col; this.sortDirection = 'asc'; }
    }
}">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-red-400">Costume Sales Breakdown</h3>
                    <input type="text" x-model="search" placeholder="Search costume..."
                        class="bg-gray-700 border-pink-500 rounded-md text-white text-sm py-1 px-3 w-64">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700 text-gray-300 text-xs uppercase">
                            <tr>
                                <th @click="sort('name')" class="px-6 py-3 text-left cursor-pointer hover:text-white">
                                    Costume Name <span x-show="sortColumn === 'name'"
                                        x-text="sortDirection === 'asc' ? '▲':'▼'"></span></th>
                                <th @click="sort('stock')"
                                    class="px-6 py-3 text-center cursor-pointer hover:text-white">Current Stock <span
                                        x-show="sortColumn === 'stock'"
                                        x-text="sortDirection === 'asc' ? '▲':'▼'"></span></th>
                                <th @click="sort('orders_count')"
                                    class="px-6 py-3 text-center cursor-pointer hover:text-white">Rental Count <span
                                        x-show="sortColumn === 'orders_count'"
                                        x-text="sortDirection === 'asc' ? '▲':'▼'"></span></th>
                                <th class="px-6 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 text-gray-200">
                            <template x-for="item in filteredItems" :key="item.id">
                                <tr>
                                    <td class="px-6 py-4 font-bold" x-text="item.name"></td>
                                    <td class="px-6 py-4 text-center text-indigo-400 font-bold" x-text="item.stock">
                                    </td>
                                    <td class="px-6 py-4 text-center text-green-400" x-text="item.orders_count"></td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        @php $editUrl = route('renter.costumes.edit', ['costume_id' => 'ID_VAL']); @endphp
                                        <a :href="'{{ $editUrl }}'.replace('ID_VAL', item.id)"
                                            class="text-indigo-400 hover:underline">Edit</a>

                                        @php $deleteUrl = route('renter.costumes.delete', ['costume_id' => 'ID_VAL']); @endphp
                                        <form :action="'{{ $deleteUrl }}'.replace('ID_VAL', item.id)" method="POST"
                                            class="inline"
                                            @submit.prevent="if(confirm('Soft delete ' + item.name + '?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:underline">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>