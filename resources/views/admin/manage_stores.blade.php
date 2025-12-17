@php
    // Prepare the data for Alpine
    $renterData = $renters->map(function($r) {
        return [
            'id' => $r->id,
            'name' => $r->name,
            'store_name' => $r->store->store_name ?? 'N/A',
            'costumes_count' => $r->costumes_count,
            'created_at' => $r->created_at->format('Y-m-d'),
            'is_active' => $r->store->is_active ?? false,
        ];
    });
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Manage All Renter Stores 🏪
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            renters: {{ json_encode($renterData) }},
            search: '',
            filterStatus: 'all',
            sortColumn: 'store_name',
            sortDirection: 'asc',

            get filteredRenters() {
                let filtered = this.renters.filter(r => {
                    // 1. Search Filter
                    const searchMatch = r.store_name.toLowerCase().includes(this.search.toLowerCase()) || 
                                      r.name.toLowerCase().includes(this.search.toLowerCase());
                    
                    // 2. Status Filter
                    let statusMatch = true;
                    if (this.filterStatus === 'active') statusMatch = r.is_active;
                    else if (this.filterStatus === 'inactive') statusMatch = !r.is_active;

                    return searchMatch && statusMatch;
                });

                // 3. Sort Logic
                return filtered.sort((a, b) => {
                    let aVal = a[this.sortColumn];
                    let bVal = b[this.sortColumn];

                    if (this.sortColumn === 'costumes_count') {
                        aVal = parseInt(aVal);
                        bVal = parseInt(bVal);
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
            }
        }">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-indigo-500">
                
                {{-- Toolbar --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <h3 class="text-2xl font-bold text-indigo-400">
                        Active Stores List (<span x-text="filteredRenters.length"></span>)
                    </h3>

                    <div class="flex flex-wrap gap-3">
                        <input type="text" x-model="search" placeholder="Search store or owner..." 
                               class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-1 px-3 w-64">
                        
                        <select x-model="filterStatus" class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-1">
                            <option value="all">ALL STATUSES</option>
                            <option value="active">ACTIVE ONLY</option>
                            <option value="inactive">INACTIVE ONLY</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th @click="sort('store_name')" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Store Name <span x-show="sortColumn === 'store_name'" x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th @click="sort('name')" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Owner Name <span x-show="sortColumn === 'name'" x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th @click="sort('costumes_count')" class="px-6 py-3 text-center text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Listings <span x-show="sortColumn === 'costumes_count'" x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th @click="sort('created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Date Joined <span x-show="sortColumn === 'created_at'" x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            <template x-for="renter in filteredRenters" :key="renter.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-pink-400">
                                        <span x-text="renter.store_name"></span>
                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-white"
                                              :class="renter.is_active ? 'bg-green-600' : 'bg-red-600'"
                                              x-text="renter.is_active ? 'ACTIVE' : 'INACTIVE'">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="renter.name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center" x-text="renter.costumes_count"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="renter.created_at"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-y-2">
                                        {{-- Actions --}}
                                        @php $viewRouteBase = route('admin.stores.view', ['user_id' => 'ID_HERE']); @endphp
                                        <a :href="'{{ $viewRouteBase }}'.replace('ID_HERE', renter.id)" class="text-indigo-400 hover:text-indigo-600 block">View/Edit Store</a>

                                        @php $toggleRouteBase = route('admin.stores.toggle_status', ['user_id' => 'ID_HERE']); @endphp
                                        <form :action="'{{ $toggleRouteBase }}'.replace('ID_HERE', renter.id)" method="POST"
                                              class="inline-block" @submit.prevent="if (confirm('Toggle status for ' + renter.store_name + '? This affects listing visibility.')) $el.submit()">
                                            @csrf
                                            <button type="submit"
                                                    class="text-white font-bold py-1 px-3 rounded text-xs transition duration-300"
                                                    :class="renter.is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                                                    x-text="renter.is_active ? 'Deactivate Store' : 'Activate Store'">
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Empty State --}}
                <div x-show="filteredRenters.length === 0" class="py-10 text-center text-gray-500">
                    No stores found matching your search.
                </div>

            </div>
        </div>
    </div>
</x-app-layout>