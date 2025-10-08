@php
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

// Get costumes owned by the current renter
$renterCostumes = Auth::user()->costumes->pluck('id');

// Fetch orders for these costumes
$orders = Order::whereIn('costume_id', $renterCostumes)
->with('user', 'costume')
->latest()
->get();

$pendingOrders = $orders->where('status', 'waiting');
$activeRentals = $orders->whereIn('status', ['confirmed', 'borrowed']);
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Renter: Incoming Orders 📥
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h3 class="text-2xl font-bold text-yellow-400 mb-6">Orders Awaiting Your Cosmic Confirmation
                ({{ $pendingOrders->count() }})</h3>

            <div class="space-y-4">
                @forelse ($pendingOrders as $order)
                {{-- Pending Order Card --}}
                <div class="bg-gray-800 p-4 rounded-lg border border-yellow-500 shadow-lg">
                    <p class="text-xs text-gray-400">Order ID: {{ $order->order_code }} | Customer:
                        {{ $order->user->name ?? 'N/A' }}</p>
                    <h4 class="text-xl font-bold text-white mt-1">Costume:
                        {{ $order->costume->name ?? 'Costume Deleted' }} (Size {{ $order->costume->size ?? 'N/A' }})
                    </h4>
                    <p class="text-sm text-gray-300">Dates: {{ $order->start_date->format('d M') }} to
                        {{ $order->end_date->format('d M') }} ({{ $order->start_date->diffInDays($order->end_date) }}
                        Days)</p>
                    <p class="text-sm text-yellow-400 font-semibold mt-2">Status: WAITING CONFIRMATION</p>
                    <div class="mt-3 space-x-2">
                        <a href="#"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">Confirm
                            Order</a>
                        <a href="#"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">Reject</a>
                        <a href="#" class="text-indigo-400 hover:text-indigo-600 text-sm">View Details</a>
                    </div>
                </div>
                @empty
                <p class="text-gray-400">No new orders awaiting confirmation. Quiet night in the cosmos.</p>
                @endforelse
            </div>

            <h3 class="text-2xl font-bold text-indigo-400 mb-6 mt-12">Confirmed/Borrowed Rentals
                ({{ $activeRentals->count() }})</h3>
            {{-- List confirmed and borrowed orders here --}}
        </div>
    </div>
</x-app-layout>