@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Order;
    use App\Models\Costume;

    $user = Auth::user();

    $costumeIds = $user->costumes->pluck('id');

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

                {{-- 1. TOP LEVEL METRICS --}}
                <div class="grid grid-cols-3 gap-6 text-center">
                    <div class="p-4 bg-indigo-700 rounded-lg shadow-xl">
                        <p class="text-sm text-indigo-200">Total Listings</p>
                        <p class="text-4xl font-extrabold text-white mt-1">{{ $costumeIds->count() }}</p>
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

                {{-- 2. ACTION NAVIGATION --}}
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

                {{-- 3. SALES ANALYTIC --}}
                <h3 class="text-2xl font-bold text-indigo-400 border-b border-gray-700 pb-3 mt-8">Sales Overview</h3>

                <div class="grid grid-cols-1 gap-8">
                    <div class="bg-gray-700 p-6 rounded-lg shadow-inner">
                        <h4 class="text-lg font-semibold text-white mb-4">Top Costumes by Rental Count
                            ({{ $costumeSales->count() }})</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-600">
                                <thead class="bg-gray-600">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Costume
                                        </th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">Rentals
                                        </th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-white uppercase">
                                            Earnings (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-gray-700 divide-y divide-gray-600 text-gray-200">
                                    @forelse ($costumeSales as $sale)
                                        <tr>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                                {{ $sale->costume->name ?? 'Deleted' }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $sale->rental_count }}</td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm">
                                                {{ number_format($sale->total_earnings, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-3 py-2 text-center text-gray-400">No sales data yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>