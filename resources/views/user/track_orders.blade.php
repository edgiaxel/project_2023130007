@php
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
// Fetch all orders for the current user, ordered by date
$orders = Order::where('user_id', Auth::id())->with('costume')->latest()->get();
$activeOrders = $orders->whereIn('status', ['waiting', 'confirmed', 'borrowed']);
$pastOrders = $orders->whereIn('status', ['returned', 'completed', 'rejected']);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            User: Track My Cosmic Orders 🗺️
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-2xl font-bold text-yellow-400 mb-6">Active Rentals ({{ $activeOrders->count() }})</h3>
            <div class="space-y-6">
                @forelse ($activeOrders as $order)
                {{-- Active Order --}}
                @php
                $color = $order->status === 'waiting' ? 'border-yellow-500' : 'border-indigo-500';
                $statusText = strtoupper($order->status);
                @endphp
                <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 {{ $color }}">
                    <h3 class="text-xl font-bold text-white">Order #{{ $order->order_code }}:
                        {{ $order->costume->name ?? 'Costume Deleted' }}</h3>
                    <p class="text-sm text-gray-400 mt-1">Rented from: {{ $order->costume->renter->name ?? 'N/A' }} |
                        Dates: {{ \Carbon\Carbon::parse($order->start_date)->format('d M') }} -
                        {{ \Carbon\Carbon::parse($order->end_date)->format('d M') }}</p>
                    <p class="text-2xl font-extrabold text-yellow-400 mt-3">STATUS: {{ $statusText }}</p>
                    <p class="text-sm text-gray-300 mt-2">Total Paid: Rp
                        {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
                @empty
                <p class="text-gray-400">No active orders found.</p>
                @endforelse
            </div>

            <h3 class="text-2xl font-bold text-green-400 mb-6 mt-12">Past Rentals ({{ $pastOrders->count() }})</h3>
            <div class="space-y-6">
                @forelse ($pastOrders as $order)
                {{-- Completed Order --}}
                @php
                $color = $order->status === 'completed' ? 'border-green-500' : 'border-red-500';
                $statusText = strtoupper($order->status);
                @endphp
                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 {{ $color }}">
                    <h3 class="text-xl font-bold text-white">Order #{{ $order->order_code }}:
                        {{ $order->costume->name ?? 'Costume Deleted' }}</h3>
                    <p class="text-sm text-gray-400 mt-1">Rented from: {{ $order->costume->renter->name ?? 'N/A' }} |
                        Dates: {{ \Carbon\Carbon::parse($order->start_date)->format('d M') }} -
                        {{ \Carbon\Carbon::parse($order->end_date)->format('d M') }}</p>
                    <p class="text-2xl font-extrabold text-green-400 mt-3">STATUS: {{ $statusText }}</p>
                    <p class="text-sm text-gray-300 mt-2">Total Paid: Rp
                        {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    @if ($order->status === 'completed')
                    <a href="#" class="text-indigo-400 hover:text-indigo-600 text-sm mt-2 block">Leave a Review</a>
                    @endif
                </div>
                @empty
                <p class="text-gray-400">No past orders found.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>