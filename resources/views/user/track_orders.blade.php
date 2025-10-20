<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            User: Track My Cosmic Orders 🗺️
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            filterStatus: 'active', // Default to active orders
            orders: {{ json_encode($orders) }},

            get filteredOrders() {
                return this.orders.filter(order => {
                    const activeStatuses = ['waiting', 'confirmed', 'borrowed'];
                    const pastStatuses = ['returned', 'completed', 'rejected'];

                    if (this.filterStatus === 'active') {
                        return activeStatuses.includes(order.status);
                    } else if (this.filterStatus === 'past') {
                        return pastStatuses.includes(order.status);
                    } else {
                        return order.status === this.filterStatus;
                    }
                });
            }
        }">

            {{-- FILTER BUTTONS --}}
            <div class="mb-6 flex space-x-4">
                <button @click="filterStatus = 'active'"
                    :class="{'bg-indigo-600 border-indigo-400': filterStatus === 'active', 'bg-gray-700 border-gray-600': filterStatus !== 'active'}"
                    class="p-3 text-white font-bold rounded-lg border">
                    Active Rentals (<span
                        x-text="orders.filter(o => ['waiting', 'confirmed', 'borrowed'].includes(o.status)).length"></span>)
                </button>
                <button @click="filterStatus = 'waiting'"
                    :class="{'bg-yellow-600 border-yellow-400': filterStatus === 'waiting', 'bg-gray-700 border-gray-600': filterStatus !== 'waiting'}"
                    class="p-3 text-white font-bold rounded-lg border">
                    Needs Confirmation (<span x-text="orders.filter(o => o.status === 'waiting').length"></span>)
                </button>
                <button @click="filterStatus = 'past'"
                    :class="{'bg-green-600 border-green-400': filterStatus === 'past', 'bg-gray-700 border-gray-600': filterStatus !== 'past'}"
                    class="p-3 text-white font-bold rounded-lg border">
                    Past History (<span
                        x-text="orders.filter(o => ['returned', 'completed', 'rejected'].includes(o.status)).length"></span>)
                </button>
            </div>

            <h3 class="text-2xl font-bold text-white mb-6">Showing: <span x-text="filterStatus.toUpperCase()"></span>
                Orders (<span x-text="filteredOrders.length"></span>)</h3>

            <div class="space-y-6">
                <template x-for="order in filteredOrders" :key="order.order_code">
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4" :class="{
                            'border-yellow-500': order.status === 'waiting',
                            'border-indigo-500': order.status === 'confirmed',
                            'border-red-500': order.status === 'borrowed',
                            'border-green-500': order.status === 'completed',
                            'border-gray-500': order.status === 'rejected' || order.status === 'returned',
                         }">

                        <h3 class="text-xl font-bold text-white"
                            x-text="`Order #${order.order_code}: ${order.costume?.name || 'Costume Deleted'}`"></h3>

                        <p class="text-sm text-gray-400 mt-1">
                            Rented from: <span x-text="order.costume?.renter?.name || 'N/A'"></span> |
                            Dates: <span
                                x-text="new Date(order.start_date).toLocaleDateString('en-US', {day:'numeric', month:'short'})"></span>
                            -
                            <span
                                x-text="new Date(order.end_date).toLocaleDateString('en-US', {day:'numeric', month:'short'})"></span>
                        </p>

                        <p class="text-2xl font-extrabold mt-3" :class="{
                                'text-yellow-400': order.status === 'waiting',
                                'text-indigo-400': order.status === 'confirmed' || order.status === 'borrowed',
                                'text-green-400': order.status === 'completed',
                                'text-red-400': order.status === 'rejected' || order.status === 'returned',
                           }" x-text="`STATUS: ${order.status.toUpperCase()}`">
                        </p>

                        <p class="text-sm text-gray-300 mt-2">
                            Total Paid: Rp <span x-text="Number(order.total_price).toLocaleString('id-ID')"></span>
                        </p>

                        <a x-show="order.status === 'completed'" href="#"
                            class="text-indigo-400 hover:text-indigo-600 text-sm mt-2 block">Leave a Review</a>
                    </div>
                </template>

                <p x-show="filteredOrders.length === 0" class="text-gray-400">
                    No orders match the current status filter.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>