<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Individual Costume Discount Management 💸
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        {{-- Initialize Alpine Object --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{
            costumes: {{ json_encode($costumes) }},
            search: '',
            filterStatus: '',
            sortColumn: 'name',
            sortDirection: 'asc',

            get filteredCostumes() {
                let filtered = this.costumes.filter(c => {
                    // 1. Search Filter (Costume Name or Renter Store)
                    const searchMatch = c.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                      (c.renter?.store?.store_name || '').toLowerCase().includes(this.search.toLowerCase());
                    
                    // 2. Status Filter
                    let statusMatch = true;
                    if (this.filterStatus === 'active') statusMatch = c.is_on_sale;
                    else if (this.filterStatus === 'inactive') statusMatch = (c.discount_value > 0 && !c.is_on_sale);
                    else if (this.filterStatus === 'none') statusMatch = (!c.discount_value || c.discount_value == 0);

                    return searchMatch && statusMatch;
                });

                // 3. Sort Logic
                return filtered.sort((a, b) => {
                    let aVal = a[this.sortColumn];
                    let bVal = b[this.sortColumn];

                    // Handle numeric columns
                    if (['original_price', 'final_price', 'discount_value'].includes(this.sortColumn)) {
                        aVal = parseFloat(aVal) || 0;
                        bVal = parseFloat(bVal) || 0;
                    } else {
                        aVal = (aVal || '').toString().toLowerCase();
                        bVal = (bVal || '').toString().toLowerCase();
                    }

                    const comparison = aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
                    return this.sortDirection === 'asc' ? comparison : -comparison;
                });
            },

            sort(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
            },

            formatRp(val) {
                return 'Rp ' + Number(val).toLocaleString('id-ID');
            }
        }">

            @if (session('status'))
                <div class="mb-4 text-sm font-medium text-green-400 p-3 bg-green-900/50 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            {{-- 1. COSTUME DISCOUNT MONITORING --}}
            <div class="bg-gray-800 rounded-lg shadow-xl p-6 border-t-4 border-red-500">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <h3 class="text-2xl font-bold text-red-400">
                        All Listed Costume Discounts
                        <a href="{{ route('admin.soft_delete.index') }}"
                            class="ml-4 text-sm text-red-400 hover:text-red-300 font-semibold">(View Trash Bin)</a>
                    </h3>

                    {{-- Filters Toolbar --}}
                    <div class="flex flex-wrap gap-3">
                        <input type="text" x-model="search" placeholder="Search costume or store..."
                            class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-1 px-3 w-64">

                        <select x-model="filterStatus"
                            class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-1">
                            <option value="">ALL DISCOUNTS</option>
                            <option value="active">ACTIVE ONLY</option>
                            <option value="inactive">INACTIVE/READY</option>
                            <option value="none">NO DISCOUNT</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th @click="sort('name')"
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Costume / Renter <span x-show="sortColumn === 'name'"
                                        x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th @click="sort('original_price')"
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Original Price <span x-show="sortColumn === 'original_price'"
                                        x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th @click="sort('discount_value')"
                                    class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Discount / Fixed <span x-show="sortColumn === 'discount_value'"
                                        x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Status</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            <template x-for="costume in filteredCostumes" :key="costume.id">
                                <tr>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <p class="font-bold text-white" x-text="costume.name"></p>
                                        <p class="text-xs text-pink-400"
                                            x-text="costume.renter?.store?.store_name || 'N/A'"></p>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        {{-- 💥 DYNAMIC STRIKETHROUGH FIX --}}
                                        <span
                                            :class="costume.is_on_sale ? 'text-gray-400 line-through text-sm' : 'text-white'"
                                            x-text="formatRp(costume.original_price)"></span>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-yellow-400 font-bold">
                                        <template x-if="costume.discount_value > 0">
                                            <div>
                                                <span
                                                    x-text="costume.discount_type === 'percentage' ? Math.round(costume.discount_value) + '%' : formatRp(costume.discount_value)"></span>
                                                <p class="text-xs text-indigo-400">Final: <span
                                                        x-text="formatRp(costume.final_price)"></span></p>
                                            </div>
                                        </template>
                                        <template x-if="!(costume.discount_value > 0)">
                                            <span class="text-gray-500 font-normal">N/A</span>
                                        </template>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-white"
                                            :class="{
                                                'bg-green-600': costume.is_on_sale,
                                                'bg-yellow-600': !costume.is_on_sale && costume.discount_value > 0,
                                                'bg-gray-600': !costume.discount_value || costume.discount_value == 0
                                              }"
                                            x-text="costume.is_on_sale ? 'ACTIVE' : (costume.discount_value > 0 ? 'INACTIVE' : 'NONE')">
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                                        {{-- Dynamic URL generator --}}
                                        @php $editUrlBase = route('renter.costumes.edit', ['costume_id' => 'ID_PLACEHOLDER', 'redirect_to' => 'discounts']); @endphp
                                        <a :href="'{{ $editUrlBase }}'.replace('ID_PLACEHOLDER', costume.id)"
                                            class="text-indigo-400 hover:text-indigo-600 bg-indigo-900/30 px-3 py-1 rounded">
                                            Edit
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Empty State --}}
                <div x-show="filteredCostumes.length === 0" class="py-10 text-center text-gray-500">
                    No costumes match your cosmic filters.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>