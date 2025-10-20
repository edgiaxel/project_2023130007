@php
    use App\Models\Order;
    use Illuminate\Support\Facades\Auth;

    $renterCostumes = Auth::user()->costumes->pluck('id');

    $orders = Order::whereIn('costume_id', $renterCostumes)
        ->with('user', 'costume')
        ->latest()
        ->get();

    $pendingOrders = $orders->where('status', 'waiting');
    $ongoingRentals = $orders->whereIn('status', ['confirmed', 'borrowed']);
    $pastOrders = $orders->whereIn('status', ['returned', 'completed', 'rejected']);
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
                            {{ $order->user->name ?? 'N/A' }}
                        </p>
                        <h4 class="text-xl font-bold text-white mt-1">Costume:
                            {{ $order->costume->name ?? 'Costume Deleted' }} (Size {{ $order->costume->size ?? 'N/A' }})
                        </h4>
                        <p class="text-sm text-gray-300">Dates: {{ $order->start_date->format('d M') }} to
                            {{ $order->end_date->format('d M') }} ({{ $order->start_date->diffInDays($order->end_date) }}
                            Days)
                        </p>
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

            <h3 class="text-2xl font-bold text-indigo-400 mb-6 mt-12">Ongoing Confirmed Rentals
                ({{ $ongoingRentals->count() }})</h3>
            <div class="space-y-4">
                @forelse ($ongoingRentals as $order)
                    @php
                        $color = $order->status === 'confirmed' ? 'border-indigo-500' : 'border-red-500';
                        $statusText = strtoupper($order->status);
                    @endphp
                    <div class="bg-gray-800 p-4 rounded-lg border border-yellow-500 shadow-lg border-l-4 {{ $color }}">
                        <p class="text-xs text-gray-400">Order ID: {{ $order->order_code }} | Customer:
                            {{ $order->user->name ?? 'N/A' }}
                        </p>
                        <h4 class="text-xl font-bold text-white mt-1">Costume:
                            {{ $order->costume->name ?? 'Costume Deleted' }} (Size {{ $order->costume->size ?? 'N/A' }})
                        </h4>
                        <p class="text-sm text-gray-300">Dates: {{ $order->start_date->format('d M') }} to
                            {{ $order->end_date->format('d M') }} ({{ $order->start_date->diffInDays($order->end_date) }}
                            Days)
                        </p>
                        <p class="text-2xl font-extrabold text-indigo-400 mt-3">STATUS: {{ $statusText }}</p>
                        <div class="mt-3 space-x-2">
                            <a href="#"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">Mark as
                                Returned</a>
                            <a href="#" class="text-indigo-400 hover:text-indigo-600 text-sm">View Details</a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">No confirmed orders are currently out for rental.</p>
                @endforelse
            </div>

            <h3 class="text-2xl font-bold text-green-400 mb-6 mt-12">Rental History (Completed/Rejected)</h3>
        </div>
    </div>
</x-app-layout>