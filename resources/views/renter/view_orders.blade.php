@php
    use App\Models\Order;
    use Illuminate\Support\Facades\Auth;

    $renterCostumes = Auth::user()->costumes->pluck('id');
    // 💥 FIX: Include 'overdue' in the query fetch
    $orders = Order::whereIn('costume_id', $renterCostumes)->whereIn('status', ['waiting', 'confirmed', 'borrowed', 'returned', 'completed', 'rejected', 'overdue'])->with('user', 'costume')->latest()->get();

    // Divide into categories
    $pendingOrders = $orders->where('status', 'waiting');
    // FIX 1: Include 'overdue' in Ongoing/Active Rentals
    $ongoingRentals = $orders->whereIn('status', ['confirmed', 'borrowed', 'returned', 'overdue']);
    $pastOrders = $orders->whereIn('status', ['completed', 'rejected']);
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
                            {{-- CONFIRM FORM --}}
                            <form action="{{ route('renter.orders.confirm', $order->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">Confirm
                                    Order</button>
                            </form>

                            {{-- REJECT FORM --}}
                            <form action="{{ route('renter.orders.reject', $order->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">Reject</button>
                            </form>

                            <a href="{{ route('costume.detail', $order->costume_id) }}" target=""
                                class="text-indigo-400 hover:text-indigo-600 text-sm">View Costume</a>
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
                        // 💥 FIX: Add color and text for overdue status
                        $color = match ($order->status) {
                            'confirmed' => 'border-indigo-500',
                            'borrowed' => 'border-red-500',
                            'overdue' => 'border-yellow-800', // Highlight overdue in dark yellow/brown
                            default => 'border-gray-500',
                        };
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
                        <p
                            class="text-2xl font-extrabold @if($order->status == 'overdue') text-yellow-500 @else text-indigo-400 @endif mt-3">
                            STATUS: {{ $statusText }}
                        </p>
                        <div class="mt-3 space-x-2">

                            {{-- 💥 NEW: MARK AS COMPLETED BUTTON (Only appears after RETURNED) --}}
                            @if ($order->status === 'returned')
                                <form action="{{ route('renter.orders.update.status', $order->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <input type="hidden" name="new_status" value="completed">
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Confirm Check & Finalize
                                    </button>
                                </form>
                            @endif

                            {{-- MARK AS RETURNED FORM (Only show if borrowed/confirmed) --}}
                            @if (in_array($order->status, ['borrowed', 'confirmed', 'overdue']))
                                <form action="{{ route('renter.orders.update.status', $order->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <input type="hidden" name="new_status" value="returned">
                                    <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Mark as Returned
                                    </button>
                                </form>
                            @endif

                            {{-- MARK AS BORROWED FORM (Only show if status is confirmed) --}}
                            @if ($order->status === 'confirmed')
                                <form action="{{ route('renter.orders.update.status', $order->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <input type="hidden" name="new_status" value="borrowed">
                                    <button type="submit"
                                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm">Mark
                                        as Borrowed</button>
                                </form>
                            @endif

                            <a href="{{ route('order.detail', $order->id) }}" target=""
                                class="text-indigo-400 hover:text-indigo-600 text-sm">View Details</a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">No confirmed orders are currently out for rental.</p>
                @endforelse
            </div>

            <h3 class="text-2xl font-bold text-green-400 mb-6 mt-12">Rental History (Completed/Rejected)</h3>
            <div class="space-y-4">
                @forelse ($pastOrders as $order)
                    @php
                        $color = ($order->status === 'completed') ? 'border-green-500' : 'border-red-800';
                        $statusText = strtoupper($order->status);
                    @endphp
                    <div class="bg-gray-800 p-4 rounded-lg shadow-lg border-l-4 {{ $color }}">
                        <p class="text-xs text-gray-400">Order ID: {{ $order->order_code }} | Customer:
                            {{ $order->user->name ?? 'N/A' }}
                        </p>
                        <h4 class="text-xl font-bold text-white mt-1">Costume:
                            {{ $order->costume->name ?? 'Costume Deleted' }} (Size {{ $order->costume->size ?? 'N/A' }})
                        </h4>
                        <p class="text-sm text-gray-300">Dates: {{ $order->start_date->format('d M') }} to
                            {{ $order->end_date->format('d M') }}
                        </p>
                        <p class="text-xl font-extrabold mt-3"
                            style="color: {{ $color === 'border-green-500' ? '#10B981' : '#F87171' }};">
                            STATUS: {{ $statusText }}
                        </p>
                        <div class="mt-3 space-x-2">
                            <a href="{{ route('order.detail', $order->id) }}"
                                class="text-indigo-400 hover:text-indigo-600 text-sm">View Details</a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">No completed or rejected orders in history.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>