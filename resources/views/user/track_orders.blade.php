<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            User: Track My Cosmic Orders 🗺️
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            filterStatus: 'active', 
            orders: {{ json_encode($orders->map(function ($o) {
    return [
        'id' => $o->id,
        'order_code' => $o->order_code,
        'status' => $o->status,
        'costume_name' => $o->costume ? $o->costume->name : 'Costume Deleted',
        'renter_name' => ($o->costume && $o->costume->renter) ? $o->costume->renter->name : 'N/A',
        'start_date' => $o->start_date->format('Y-m-d'),
        'end_date' => $o->end_date->format('Y-m-d'),
        'total_price' => $o->total_price,
        'has_review' => $o->review !== null,
    ];
})) }},

            get filteredOrders() {
                let filtered = this.orders.filter(order => {
                    const activeStatuses = ['waiting', 'confirmed', 'borrowed'];
                    const pastStatuses = ['returned', 'completed', 'rejected'];

                    if (this.filterStatus === 'active') {
                        return activeStatuses.includes(order.status);
                    } else if (this.filterStatus === 'past') {
                        return pastStatuses.includes(order.status);
                    } else if (this.filterStatus === 'all') { // 💥 NEW ALL FILTER
                        return true;
                    } else {
                        return order.status === this.filterStatus;
                    }
                });
                
                // 💥 SORTING: Bring unreviewed completed orders to the top, then newest first
                return filtered.sort((a, b) => {
                    if (a.status === 'completed' && !a.has_review && (b.status !== 'completed' || b.has_review)) return -1;
                    if (b.status === 'completed' && !b.has_review && (a.status !== 'completed' || a.has_review)) return 1;
                    return b.id - a.id; // Sort by ID descending (newest first) for the rest
                });
            }
        }">

            {{-- FILTER NAVIGATION --}}
            <div class="mb-8 flex flex-wrap gap-3">
                {{-- Grouped Filters --}}
                <button @click="filterStatus = 'active'"
                    :class="filterStatus === 'active' ? 'bg-indigo-600 border-indigo-400' : 'bg-gray-800 border-gray-700'"
                    class="px-4 py-2 text-white text-sm font-bold rounded-full border transition hover:bg-indigo-500">
                    Active Rentals (<span
                        x-text="orders.filter(o => ['waiting', 'confirmed', 'borrowed'].includes(o.status)).length"></span>)
                </button>

                <button @click="filterStatus = 'past'"
                    :class="filterStatus === 'past' ? 'bg-green-700 border-green-500' : 'bg-gray-800 border-gray-700'"
                    class="px-4 py-2 text-white text-sm font-bold rounded-full border transition hover:bg-green-600">
                    History (<span
                        x-text="orders.filter(o => ['returned', 'completed', 'rejected'].includes(o.status)).length"></span>)
                </button>

                <button @click="filterStatus = 'all'"
                    :class="filterStatus === 'all' ? 'bg-gray-600 border-gray-400' : 'bg-gray-800 border-gray-700'"
                    class="px-4 py-2 text-white text-sm font-bold rounded-full border transition hover:bg-gray-500">
                    All Orders (<span x-text="orders.length"></span>)
                </button>

                {{-- Status Specific Dropdown --}}
                <div class="ml-auto">
                    <select x-model="filterStatus"
                        class="bg-gray-800 text-indigo-400 text-sm border-gray-700 rounded-lg focus:ring-indigo-500">
                        <option value="active">Filter by exact status...</option>
                        <option value="waiting">Waiting Confirmation</option>
                        <option value="confirmed">Confirmed / Ready</option>
                        <option value="borrowed">Borrowed / Out</option>
                        <option value="returned">Returned</option>
                        <option value="completed">Completed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></span>
                Showing: <span x-text="filterStatus.toUpperCase()" class="text-indigo-400"></span>
            </h3>

            <div class="space-y-6">
                <template x-for="order in filteredOrders" :key="order.order_code">
                    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-8 transition duration-300 hover:bg-gray-750"
                        :class="{
                            'border-yellow-500': order.status === 'waiting',
                            'border-indigo-500': order.status === 'confirmed',
                            'border-red-500': order.status === 'borrowed',
                            'border-green-500': order.status === 'completed',
                            'border-gray-500': order.status === 'rejected' || order.status === 'returned',
                         }">

                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-white"
                                    x-text="`Order #${order.order_code}: ${order.costume_name}`"></h3>
                                <p class="text-sm text-gray-400 mt-1">
                                    Store: <span x-text="order.renter_name" class="text-pink-400"></span> |
                                    Rental: <span
                                        x-text="new Date(order.start_date).toLocaleDateString('en-GB')"></span> to <span
                                        x-text="new Date(order.end_date).toLocaleDateString('en-GB')"></span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Total Paid</p>
                                <p class="text-lg font-bold text-white"
                                    x-text="'Rp ' + Number(order.total_price).toLocaleString('id-ID')"></p>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between items-center border-t border-gray-700 pt-4">
                            <div>
                                <p class="text-sm font-black tracking-widest" :class="{
                                    'text-yellow-400': order.status === 'waiting',
                                    'text-indigo-400': order.status === 'confirmed' || order.status === 'borrowed',
                                    'text-green-400': order.status === 'completed',
                                    'text-red-400': order.status === 'rejected',
                                    'text-gray-400': order.status === 'returned',
                                }" x-text="order.status.toUpperCase()"></p>
                            </div>

                            <div>
                                {{-- Review Button Logic --}}
                                <div x-show="order.status === 'completed' && !order.has_review">
                                    @php $reviewUrl = route('user.review.create', ['order_id' => 'ID_VAL']); @endphp
                                    <a :href="'{{ $reviewUrl }}'.replace('ID_VAL', order.id)"
                                        class="inline-block bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-bold py-2 px-4 rounded shadow-lg transition animate-bounce">
                                        LEAVE A REVIEW ⭐
                                    </a>
                                </div>
                                <p x-show="order.has_review"
                                    class="text-xs text-green-500 font-bold flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Review Submitted
                                </p>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty State --}}
                <div x-show="filteredOrders.length === 0" class="py-20 text-center">
                    <div class="bg-gray-800 inline-block p-6 rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-lg italic">No transmissions found in this sector of the catalog.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>