@php
use App\Models\Order;
// Fetch ALL orders and eager load the related costume and the costume's renter/user.
$orders = Order::with(['costume.renter', 'user'])->latest()->limit(10)->get();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Monitor All Cosmic Transactions 📈
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-red-500">
                <h3 class="text-lg font-bold text-gray-200 mb-4">Current Rental Overview</h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Order ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Costume</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Renter</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Customer</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Total Price</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            @forelse ($orders as $order)
                            @php
                            $statusColor = ['waiting' => 'text-yellow-400', 'confirmed' => 'text-indigo-400', 'borrowed'
                            => 'text-red-400', 'completed' => 'text-green-400'][$order->status] ?? 'text-gray-400';
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->order_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->costume->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->costume->renter->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp
                                    {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap {{ $statusColor }} font-semibold">
                                    {{ strtoupper($order->status) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-400">No transactions recorded
                                    yet, slackers.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>