<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin Control Deck 🚀 <span class="text-red-400">GLOBAL OVERVIEW <a
                    href="{{ route('admin.soft_delete.index') }}"
                    class="ml-4 text-sm text-red-400 hover:text-red-300 font-semibold">(View Trash Bin)</a></span>
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            <div class="grid grid-cols-4 gap-6 text-center">
                <div class="p-6 bg-gray-800 rounded-lg shadow-xl border-t-4 border-indigo-500">
                    <p class="text-sm text-gray-400">Total Revenue (Completed)</p>
                    <p class="text-2xl font-extrabold text-green-400 mt-2">Rp
                        {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
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
            <div class="bg-gray-800 rounded-lg shadow-xl p-6" x-data="{
    search: '',
    sortColumn: 'revenue',
    sortDirection: 'desc',
    renters: {{ json_encode($renterSummaries->values()) }},

    get filteredRenters() {
        let filtered = this.renters.filter(r => 
            r.name.toLowerCase().includes(this.search.toLowerCase())
        );

        return filtered.sort((a, b) => {
            let aVal = a[this.sortColumn];
            let bVal = b[this.sortColumn];

            // Numeric columns
            if (['revenue', 'total_costumes', 'active_costumes'].includes(this.sortColumn)) {
                aVal = parseFloat(aVal) || 0;
                bVal = parseFloat(bVal) || 0;
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
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h3 class="text-2xl font-bold text-red-400">Top Renters by Total Revenue</h3>
                    <div class="flex gap-4">
                        <input type="text" x-model="search" placeholder="Search Renter..."
                            class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-1 px-3">
                        <a href="{{ route('exports.analytics', ['format' => 'excel']) }}"
                            class="p-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg text-sm">
                            Export (.xlsx) 📊
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700 text-gray-300 text-xs uppercase">
                            <tr>
                                <th @click="sort('name')" class="px-6 py-3 text-left cursor-pointer hover:text-white">
                                    Renter <span x-show="sortColumn === 'name'"
                                        x-text="sortDirection === 'asc' ? '▲':'▼'"></span></th>
                                <th @click="sort('revenue')"
                                    class="px-6 py-3 text-left cursor-pointer hover:text-white">Revenue <span
                                        x-show="sortColumn === 'revenue'"
                                        x-text="sortDirection === 'asc' ? '▲':'▼'"></span></th>
                                <th @click="sort('active_costumes')"
                                    class="px-6 py-3 text-left cursor-pointer hover:text-white">Costumes <span
                                        x-show="sortColumn === 'active_costumes'"
                                        x-text="sortDirection === 'asc' ? '▲':'▼'"></span></th>
                                <th class="px-6 py-3 text-left">Top Item</th>
                                <th class="px-6 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 text-gray-200">
                            <template x-for="renter in filteredRenters" :key="renter.id">
                                <tr>
                                    <td class="px-6 py-4 font-bold text-white" x-text="renter.name"></td>
                                    <td class="px-6 py-4 text-green-400 font-bold"
                                        x-text="'Rp ' + Number(renter.revenue).toLocaleString('id-ID')"></td>
                                    <td class="px-6 py-4 text-sm">
                                        <p class="text-green-400">Live: <span x-text="renter.active_costumes"></span>
                                        </p>
                                        <p class="text-yellow-400">Pending: <span
                                                x-text="renter.pending_costumes"></span></p>
                                        <p class="text-gray-400 border-t border-gray-700 mt-1">Total: <span
                                                x-text="renter.total_costumes"></span></p>
                                    </td>
                                    <td class="px-6 py-4 text-sm italic" x-text="renter.top_costume"></td>
                                    <td class="px-6 py-4">
                                        @php $analyticsUrl = route('admin.renters.analytics', ['user_id' => 'ID_VAL']); @endphp
                                        <a :href="'{{ $analyticsUrl }}'.replace('ID_VAL', renter.id)"
                                            class="text-indigo-400 hover:underline">View</a>
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