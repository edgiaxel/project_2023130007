<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight flex justify-between items-center">
            Renter: Costume Management Deck 🌌
            <a href="{{ route('renter.costumes.upload') }}"
                class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition duration-300">
                + Upload New Costume
            </a>
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            sortColumn: 'name', 
            sortDirection: 'asc',
            filterStatus: '',
            costumes: {{ json_encode($costumes) }}, 

            get filteredCostumes() {
                // 1. Filter Logic
                let filtered = this.costumes.filter(costume => 
                    !this.filterStatus || 
                    (this.filterStatus === 'live' && costume.is_approved) ||
                    (this.filterStatus === 'pending' && !costume.is_approved)
                );

                // 2. Sort Logic
                return filtered.sort((a, b) => {
                    let aVal = a[this.sortColumn] || '';
                    let bVal = b[this.sortColumn] || '';

                    // Convert numbers/booleans for correct sorting
                    if (this.sortColumn === 'stock' || this.sortColumn === 'price_per_day') {
                        aVal = parseFloat(aVal);
                        bVal = parseFloat(bVal);
                    } else if (this.sortColumn === 'is_approved') {
                        // Sort boolean values (false/pending first)
                        aVal = a.is_approved;
                        bVal = b.is_approved;
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
                <h3 class="text-2xl font-bold text-white mb-6">My Current Catalog (<span
                        x-text="costumes.length"></span> Listings)</h3>

                <div class="flex space-x-4 mb-4 items-center">
                    <label for="filterStatus" class="text-gray-400">Filter Status:</label>
                    <select x-model="filterStatus" id="filterStatus"
                        class="bg-gray-700 border-pink-500 rounded-md text-white text-sm py-2 px-3">
                        <option value="">ALL STATUSES</option>
                        <option value="live">LIVE (Approved)</option>
                        <option value="pending">PENDING ADMIN</option>
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                @foreach (['Costume Name' => 'name', 'Series' => 'series', 'Stock' => 'stock', 'Price/Day' => 'price_per_day', 'Status' => 'is_approved'] as $label => $column)
                                    <th @click="sort('{{ $column }}')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                        {{ $label }}
                                        <span x-show="sortColumn === '{{ $column }}'"
                                            x-text="sortDirection === 'asc' ? ' ▲' : ' ▼'"></span>
                                    </th>
                                @endforeach
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            <template x-for="costume in filteredCostumes" :key="costume.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="costume.name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="costume.series"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-lg font-bold text-indigo-400"
                                        x-text="costume.stock"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">Rp <span
                                            x-text="Number(costume.price_per_day).toLocaleString('id-ID')"></span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-white"
                                            :class="{'bg-green-700': costume.is_approved, 'bg-yellow-700': !costume.is_approved}"
                                            x-text="costume.is_approved ? 'LIVE' : 'PENDING ADMIN'">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        {{-- 🤬 EDIT LINK FIX: Force the path structure and use Alpine for ID injection
                                        --}}
                                        @php
                                            // Generate the base URL structure using a dummy placeholder ID (e.g., /costumes/XX/edit)
                                            // We use 'XX' instead of '0' for cleaner replacement later.
                                            $editUrlBase = route('renter.costumes.edit', ['costume_id' => 'XX']); 
                                        @endphp

                                        <a :href="'{{ $editUrlBase }}'.replace('XX', costume.id)"
                                            class="text-indigo-400 hover:text-indigo-600">Edit</a>

                                        {{-- DELETE (SOFT) FORM FIX: Use the same replacement method --}}
                                        @php
                                            $deleteUrlBase = route('renter.costumes.delete', ['costume_id' => 'XX']);
                                        @endphp
                                        <form :action="'{{ $deleteUrlBase }}'.replace('XX', costume.id)" method="POST"
                                            class="inline"
                                            @submit.prevent="if (confirm('Are you sure you want to soft delete ' + costume.name + '?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600">Delete
                                                (Soft)</button>
                                        </form>
                                    </td>
                                </tr>
                            </template>

                            <tr x-show="filteredCostumes.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                                    No costumes match the current filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>