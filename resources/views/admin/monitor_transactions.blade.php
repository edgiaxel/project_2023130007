<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Monitor All Cosmic Transactions 📈
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ 
            sortColumn: 'order_code', 
            sortDirection: 'asc',
            filterStatus: '',
            orders: {{ json_encode($orders) }}, 

           get filteredOrders() {
                // 1. Filter by Status
                let filtered = this.orders.filter(order => 
                    !this.filterStatus || order.status === this.filterStatus
                );

                // 2. Sort Logic
                return filtered.sort((a, b) => {
                    let aVal = a[this.sortColumn] || '';
                    let bVal = b[this.sortColumn] || '';

                    // Handle nested properties (Costume Name, Renter Name)
                    if (this.sortColumn === 'costume_name') {
                        aVal = a.costume?.name || '';
                        bVal = b.costume?.name || '';
                    } else if (this.sortColumn === 'renter_name') {
                        aVal = a.costume?.renter?.name || '';
                        bVal = b.costume?.renter?.name || '';
                    } else if (this.sortColumn === 'customer_name') {
                        aVal = a.user?.name || '';
                        bVal = b.user?.name || '';
                    }

                    // Numeric Comparison for Price
                    if (this.sortColumn === 'total_price') {
                        aVal = parseFloat(aVal);
                        bVal = parseFloat(bVal);
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

            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-red-500">
                <h3 class="text-2xl font-bold text-gray-200 mb-4">Current Rental Overview ({{ $orders->count() }} Total)
                </h3>

                <div class="flex space-x-4 mb-4 items-center">
                    {{-- Status Filter Dropdown --}}
                    <label for="filterStatus" class="text-gray-400">Filter Status:</label>
                    <select x-model="filterStatus" id="filterStatus"
                        class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-2 px-3">
                        <option value="">ALL STATUSES</option>
                        <option value="waiting">WAITING</option>
                        <option value="confirmed">CONFIRMED</option>
                        <option value="borrowed">BORROWED</option>
                        <option value="returned">RETURNED</option>
                        <option value="completed">COMPLETED</option>
                        <option value="rejected">REJECTED</option>
                    </select>

                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                {{-- Clickable Headers for Sorting --}}
                                @foreach (['Order ID' => 'order_code', 'Costume' => 'costume_name', 'Renter' => 'renter_name', 'Customer' => 'customer_name', 'Total Price' => 'total_price', 'Status' => 'status'] as $label => $column)
                                    <th @click="sort('{{ $column }}')"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                        {{ $label }}
                                        <span x-show="sortColumn === '{{ $column }}'"
                                            x-text="sortDirection === 'asc' ? ' ▲' : ' ▼'"></span>
                                    </th>
                                @endforeach
                                {{-- ADDED ACTIONS HEADER --}}
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            <template x-for="order in filteredOrders" :key="order.order_code">
                                <tr :class="{'bg-gray-900': order.status === 'borrowed'}">
                                    <td class="px-8 py-4 whitespace-nowrap" x-text="order.order_code"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="order.costume?.name || 'N/A'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap"
                                        x-text="order.costume?.renter?.name || 'N/A'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="order.user?.name || 'N/A'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        Rp <span x-text="Number(order.total_price).toLocaleString('id-ID')"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-semibold" :class="{
                                        'text-yellow-400': order.status === 'waiting',
                                        'text-indigo-400': order.status === 'confirmed',
                                        'text-red-400': order.status === 'borrowed',
                                        'text-green-400': order.status === 'completed',
                                        'text-gray-400': order.status === 'rejected' || order.status === 'returned',
                                    }" x-text="order.status.toUpperCase()">
                                    </td>

                                    {{-- ADDED ACTIONS COLUMN (Status Dropdown + View Link) --}}
                                    <td class="px-6 py-4 whitespace-nowrap space-y-1">
                                        <select @change="
                                                let baseUrl;
                                                let newStatus = $event.target.value;
                                                let orderId = order.id; // 1. Get the current order ID from Alpine loop

                                                if (newStatus === 'reject') {
                                                    baseUrl = `{{ route('renter.orders.reject', ['order_id' => '__ID__']) }}`;
                                                } else if (newStatus) {
                                                    baseUrl = `{{ route('renter.orders.update.status', ['order_id' => '__ID__']) }}`;
                                                }

                                                if (newStatus) {
                                                    let url = baseUrl.replace('__ID__', orderId); // 2. Build final URL
                                                    
                                                    let bodyData = {
                                                        _token: '{{ csrf_token() }}', // 3. CSRF Token
                                                        new_status: newStatus 
                                                    };

                                                    if (newStatus === 'reject') {
                                                        // Rejects use their own dedicated controller action, no need for new_status in body
                                                        delete bodyData.new_status; 
                                                    }

                                                    fetch(url, {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify(bodyData)
                                                    }).then(response => {
                                                        // Check for non-200 responses before reloading
                                                        if (response.ok) {
                                                            window.location.reload(); // Refresh on success
                                                        } else {
                                                            alert('Status update failed! Check permissions or order status history.');
                                                        }
                                                    });
                                                }" class="bg-gray-700 border-gray-600 text-white text-xs rounded-md w-full">

                                            {{-- Options remain unchanged, relying on Alpine loop data --}}
                                            <option value="">CHANGE STATUS</option>
                                            <option value="confirmed" :disabled="order.status !== 'waiting'"
                                                :selected="order.status === 'confirmed'">Confirmed</option>
                                            <option value="borrowed" :disabled="order.status !== 'confirmed'"
                                                :selected="order.status === 'borrowed'">Borrowed (Out for Rent)</option>
                                            <option value="returned"
                                                :disabled="order.status === 'rejected' || order.status === 'completed'"
                                                :selected="order.status === 'returned'">Returned (Check-in)</option>
                                            <option value="completed" :disabled="order.status !== 'returned'"
                                                :selected="order.status === 'completed'">Completed (Finalized)</option>
                                            <option value="reject" :disabled="order.status !== 'waiting'">REJECT
                                                (Permanent)</option>
                                        </select>

                                        <a :href="`{{ route('order.detail', ['order_id' => '__ID__']) }}`.replace('__ID__', order.id)"
                                            target="" class="text-sm text-indigo-400 hover:text-indigo-600 block mt-1">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            </template>

                            <tr x-show="filteredOrders.length === 0">
                                <td colspan="7" class="px-6 py-4 text-center text-gray-400">
                                    No transactions match the current filter or criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>