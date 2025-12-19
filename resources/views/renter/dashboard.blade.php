@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Order;
    use App\Models\Costume;

    $user = Auth::user();
    // 💥 NEW: Fetch the associated store status
    $store = $user->store;
    $storeIsActive = $store && $store->is_active;

    // Costumes owned by the user
    $userCostumes = $user->costumes(); // Use the relationship query builder for flexibility

    // 💥 FIX: Get costume counts by status
    $activeCostumes = (clone $userCostumes)->where('status', 'approved')->count();
    $pendingCostumes = (clone $userCostumes)->where('status', 'pending')->count();
    $rejectedCostumes = (clone $userCostumes)->where('status', 'rejected')->count();

    // Re-fetch costume IDs from the query builder for order calculation
    $costumeIds = $userCostumes->pluck('id');

    $totalOrders = Order::whereIn('costume_id', $costumeIds)->count();
    $activeOrders = Order::whereIn('costume_id', $costumeIds)
        ->whereIn('status', ['waiting', 'confirmed', 'borrowed'])
        ->count();
    $totalRevenue = Order::whereIn('costume_id', $costumeIds)
        ->whereIn('status', ['completed', 'borrowed'])
        ->sum('total_price');

    $costumeSales = Order::selectRaw('costume_id, COUNT(*) as rental_count, SUM(total_price) as total_earnings')
        ->whereIn('costume_id', $costumeIds)
        ->whereIn('status', ['completed', 'borrowed'])
        ->groupBy('costume_id')
        ->with('costume')
        ->orderByDesc('rental_count')
        ->get();

    $monthlyRevenue = [
        (object) ['month' => 'Jan', 'sales' => 1200000],
        (object) ['month' => 'Feb', 'sales' => 1500000],
        (object) ['month' => 'Mar', 'sales' => 1800000],
        (object) ['month' => 'Apr', 'sales' => 900000],
    ];
    $monthlyRevenueJson = json_encode(array_column($monthlyRevenue, 'sales'));
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Renter Store Panel ✨ <span class="text-pink-400">Your Cosmic Catalog</span>
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 bg-opacity-90 overflow-hidden shadow-xl sm:rounded-lg p-8 space-y-10">
                <p class="text-3xl font-extrabold text-pink-400">Welcome back, {{ $user->name }}.</p>

                @if (!$store || !$storeIsActive)
                    <div class="p-6 bg-red-800 rounded-lg border border-red-400 text-white text-center">
                        <h4 class="text-2xl font-bold mb-2">⚠️ STORE DEACTIVATED</h4>
                        <p class="text-lg">Your rental storefront is currently **INACTIVE** by Admin action. You cannot
                            manage costumes or view orders until the store is reactivated.</p>
                    </div>
                @endif

                {{-- 1. TOP LEVEL METRICS --}}
                <div class="grid grid-cols-3 gap-6 text-center">
                    <div class="p-4 bg-indigo-700 rounded-lg shadow-xl">
                        <p class="text-sm text-indigo-200">Listing Status</p>
                        {{-- Display segmented counts --}}
                        <div class="flex gap-6 justify-center mt-2">
                            <p class="text-xl font-semibold text-green-400">Live: {{ $activeCostumes }}</p>
                            <p class="text-xl font-semibold text-yellow-400">Pending: {{ $pendingCostumes }}</p>
                            <p class="text-xl font-semibold text-red-400">Rejected: {{ $rejectedCostumes }}</p>
                        </div>
                    </div>
                    <div class="p-4 bg-yellow-700 rounded-lg shadow-xl">
                        <p class="text-sm text-yellow-200">Active Rentals / Pending</p>
                        <p class="text-4xl font-extrabold text-white mt-1">{{ $activeOrders }}</p>
                    </div>
                    <div class="p-4 bg-green-700 rounded-lg shadow-xl">
                        <p class="text-sm text-green-200">Total Revenue</p>
                        <p class="text-3xl font-extrabold text-white mt-1">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                @if ($storeIsActive)
                    {{-- 2. ACTION NAVIGATION (Only visible if active) --}}
                    <div class="mt-8 grid grid-cols-3 gap-6 text-center">
                        <a href="{{ route('renter.profile.setup') }}"
                            class="block p-6 bg-pink-700 hover:bg-pink-600 rounded-lg shadow-lg transition duration-300">
                            <span class="text-3xl font-extrabold text-white">Setup Profile</span>
                        </a>
                        <a href="{{ route('renter.costumes.manage') }}"
                            class="block p-6 bg-indigo-700 hover:bg-indigo-600 rounded-lg shadow-lg transition duration-300">
                            <span class="text-3xl font-extrabold text-white">Manage Costumes</span>
                        </a>
                        <a href="{{ route('renter.orders') }}"
                            class="block p-6 bg-yellow-700 hover:bg-yellow-600 rounded-lg shadow-lg transition duration-300">
                            <span class="text-3xl font-extrabold text-white">View Orders</span>
                        </a>
                    </div>
                @else
                    <div class="mt-8 grid grid-cols-3 gap-6 text-center opacity-50 cursor-not-allowed">
                        <span class="block p-6 bg-pink-700 rounded-lg shadow-lg">
                            <span class="text-3xl font-extrabold text-white">Setup Profile</span>
                        </span>
                        <span class="block p-6 bg-indigo-700 rounded-lg shadow-lg">
                            <span class="text-3xl font-extrabold text-white">Manage Costumes</span>
                        </span>
                        <span class="block p-6 bg-yellow-700 rounded-lg shadow-lg">
                            <span class="text-3xl font-extrabold text-white">View Orders</span>
                        </span>
                    </div>
                @endif

                {{-- 3. SALES OVERVIEW WITH ALPINE.JS --}}
                <div x-data="{
    search: '',
    sortColumn: 'rental_count',
    sortDirection: 'desc',
    sales: {{ json_encode($costumeSales->map(function ($s) {
    return [
        'id' => $s->costume_id,
        'name' => $s->costume->name ?? 'Deleted Costume',
        'rental_count' => (int) $s->rental_count,
        'total_earnings' => (float) $s->total_earnings,
        'favorites' => $s->costume->favoritedBy()->count()
    ];
})) }},

    get filteredSales() {
        let filtered = this.sales.filter(s => 
            s.name.toLowerCase().includes(this.search.toLowerCase())
        );

        return filtered.sort((a, b) => {
            let aVal = a[this.sortColumn];
            let bVal = b[this.sortColumn];

            // Sorting logic
            const comp = aVal > bVal ? 1 : (aVal < bVal ? -1 : 0);
            return this.sortDirection === 'asc' ? comp : -comp;
        });
    },

    sort(col) {
        if(this.sortColumn === col) this.sortDirection = (this.sortDirection === 'asc' ? 'desc' : 'asc');
        else { this.sortColumn = col; this.sortDirection = 'asc'; }
    },

    formatRp(val) {
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    }
}">
                    <div
                        class="flex flex-col md:flex-row justify-between items-center border-b border-gray-700 pb-3 mt-8 mb-6 gap-4">
                        <h3 class="text-2xl font-bold text-indigo-400">Sales Overview</h3>

                        <div class="flex flex-wrap gap-3">
                            {{-- Search Input --}}
                            <input type="text" x-model="search" placeholder="Search costume name..."
                                class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-1 px-3 w-64">

                            {{-- 💥 NEW: EXPORT BUTTON FOR RENTER --}}
                            <a href="{{ route('exports.analytics', ['format' => 'excel']) }}"
                                class="inline-block p-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md transition duration-300 text-sm">
                                Download Sales Report 📊
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8">
                        <div class="bg-gray-700 p-6 rounded-lg shadow-inner">
                            <h4 class="text-lg font-semibold text-white mb-4">
                                Costume Performance (<span x-text="filteredSales.length"></span> Items)
                            </h4>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-600">
                                    <thead class="bg-gray-600">
                                        <tr>
                                            <th @click="sort('name')"
                                                class="px-3 py-2 text-left text-xs font-medium text-white uppercase tracking-wider cursor-pointer hover:bg-gray-500 transition">
                                                Costume <span x-show="sortColumn === 'name'"
                                                    x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                            </th>
                                            <th @click="sort('rental_count')"
                                                class="px-3 py-2 text-center text-xs font-medium text-white uppercase tracking-wider cursor-pointer hover:bg-gray-500 transition">
                                                Rentals <span x-show="sortColumn === 'rental_count'"
                                                    x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                            </th>
                                            <th @click="sort('total_earnings')"
                                                class="px-3 py-2 text-right text-xs font-medium text-white uppercase tracking-wider cursor-pointer hover:bg-gray-500 transition">
                                                Earnings <span x-show="sortColumn === 'total_earnings'"
                                                    x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                            </th>
                                            <th class="px-3 py-2 text-center text-white uppercase">Wishlist</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-gray-700 divide-y divide-gray-600 text-gray-200">
                                        <template x-for="sale in filteredSales" :key="sale.id">
                                            <tr>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm font-bold text-white"
                                                    x-text="sale.name"></td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-center text-indigo-300 font-extrabold"
                                                    x-text="sale.rental_count"></td>
                                                <td class="px-3 py-4 whitespace-nowrap text-sm text-right text-green-400 font-mono"
                                                    x-text="formatRp(sale.total_earnings)"></td>
                                                <td class="px-3 py-4 text-center text-pink-400 font-bold"
                                                    x-text="sale.favorites"></td>
                                            </tr>
                                        </template>

                                        {{-- Empty State --}}
                                        <tr x-show="filteredSales.length === 0">
                                            <td colspan="3" class="px-3 py-10 text-center text-gray-400 italic">
                                                No cosmic sales found matching your search.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>