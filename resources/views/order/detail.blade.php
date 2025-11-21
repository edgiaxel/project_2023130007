@php
    use App\Models\Order;
    use Illuminate\Support\Facades\Route;
    use Carbon\Carbon;
    // This view expects the $order variable to be passed from a Controller.
    // If accessing directly via route::view (bad practice!), we fetch it here:
    $orderId = Route::current()->parameter('order_id');
    $order = $order ?? Order::with(['costume.renter.store', 'user'])->findOrFail($orderId);

    $statusColor = [
        'waiting' => 'border-yellow-500',
        'confirmed' => 'border-indigo-500',
        'borrowed' => 'border-red-500',
        'returned' => 'border-gray-500',
        'completed' => 'border-green-500',
        'rejected' => 'border-red-800'
    ][$order->status] ?? 'border-gray-600';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Order Details: <span class="text-pink-400">#{{ $order->order_code }}</span> 🌙
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow-xl sm:rounded-lg p-8 space-y-6 border-l-4 {{ $statusColor }}">

                <div class="flex justify-between items-start border-b border-gray-700 pb-4">
                    <div>
                        <p class="text-sm text-gray-400">Order Placed On: {{ $order->created_at->format('Y-m-d H:i') }}
                        </p>
                        <p class="text-4xl font-extrabold mt-2"
                            style="color: {{ $statusColor === 'border-green-500' ? '#10B981' : '#F59E0B' }};">
                            STATUS: {{ strtoupper($order->status) }}
                        </p>
                    </div>

                    {{-- Action Button based on User Role/Status --}}
                    <div>
                        @if (Auth::user()->hasRole('admin'))
                            <a href="{{ route('admin.transactions') }}" class="text-red-400 hover:underline text-sm">&larr;
                                Back to Admin Monitor</a>
                        @elseif (Auth::user()->hasRole('renter'))
                            <a href="{{ route('renter.orders') }}" class="text-red-400 hover:underline text-sm">&larr; Back
                                to Renter Orders</a>
                        @else
                            <a href="{{ route('user.orders') }}" class="text-red-400 hover:underline text-sm">&larr; Back to
                                My Orders</a>
                        @endif
                    </div>
                </div>

                {{-- 2. COSTUME AND RENTER INFO --}}
                <div class="grid grid-cols-3 gap-6 text-gray-300 border-b border-gray-700 pb-6">
                    <div class="col-span-1">
                        <p class="text-lg font-bold text-indigo-400 mb-2">Costume Ordered</p>
                        <a href="{{ route('costume.detail', $order->costume_id) }}"
                            class="text-pink-400 hover:underline">
                            <p class="text-xl font-semibold">{{ $order->costume->name ?? 'N/A' }}</p>
                        </a>
                        <p class="text-sm mt-1">Series: {{ $order->costume->series ?? 'N/A' }}</p>
                        <p class="text-sm">Size: {{ $order->costume->size ?? 'N/A' }}</p>
                    </div>
                    <div class="col-span-1">
                        <p class="text-lg font-bold text-indigo-400 mb-2">Renter/Store</p>
                        <p class="text-white">{{ $order->costume->renter->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $order->costume->renter->store->store_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-span-1">
                        <p class="text-lg font-bold text-indigo-400 mb-2">Customer</p>
                        <p>{{ $order->user->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $order->user->email ?? 'N/A' }}</p>
                    </div>
                </div>

                {{-- 3. FINANCIALS AND DATES --}}
                <div class="grid grid-cols-3 gap-6 text-gray-300">
                    <div>
                        <p class="text-lg font-bold text-green-400 mb-2">Rental Period</p>
                        <p>Start: {{ $order->start_date->format('Y-m-d') }}</p>
                        <p>End: {{ $order->end_date->format('Y-m-d') }}</p>
                        <p class="text-sm text-gray-500 mt-1">Duration:
                            {{ $order->start_date->diffInDays($order->end_date) + 1 }} days</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-lg font-bold text-green-400 mb-2">Financial Breakdown</p>
                        <p>Price per Day: Rp {{ number_format($order->costume->price_per_day ?? 0, 0, ',', '.') }}</p>
                        <p class="text-2xl font-extrabold text-white mt-3">
                            TOTAL PAID: Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- 4. RENTER/ADMIN ACTION LOGIC (Quick Status Update) --}}
                @if (Auth::user()->hasRole('admin') || Auth::id() === $order->costume->user_id)
                    <div class="mt-8 pt-6 border-t border-gray-700">
                        <p class="text-lg font-bold text-red-400 mb-3">Update Order Status</p>

                        <form x-data="{ newStatus: '' }" @submit.prevent="
                                    let url;
                                    let body = {};

                                    if (newStatus === 'reject') {
                                        url = '{{ route('renter.orders.reject', $order->id) }}';
                                    } else {
                                        url = '{{ route('renter.orders.update.status', $order->id) }}';
                                        body.new_status = newStatus;
                                    }

                                    fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify(body)
                                    }).then(response => {
                                        if (response.ok) {
                                            window.location.reload(); // Reload to see the status change
                                        } else {
                                            alert('Error updating status. Check console.');
                                        }
                                    });
                                  " class="flex space-x-4 items-center">

                            <select x-model="newStatus" required
                                class="bg-gray-700 border-red-500 rounded-md text-white text-sm py-2 px-3">
                                <option value="">Select New Status</option>
                                <option value="confirmed" {{ $order->status === 'confirmed' ? 'disabled' : '' }}>Confirmed
                                    (Ready for Pickup)</option>
                                <option value="borrowed" {{ $order->status === 'borrowed' ? 'disabled' : '' }}>Borrowed (Out
                                    for Rent)</option>
                                <option value="returned" {{ $order->status === 'returned' || $order->status === 'completed' ? 'disabled' : '' }}>Returned (Awaiting Check)</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'disabled' : '' }}>Completed
                                    (Finalize)</option>
                                <option value="rejected" {{ $order->status === 'rejected' ? 'disabled' : '' }}>Reject
                                    (Pending/Waiting only)</option>
                            </select>

                            <x-primary-button type="submit" class="bg-red-600 hover:bg-red-700"
                                x-bind:disabled="newStatus === '' || '{{ $order->status }}' === 'completed' || '{{ $order->status }}' === 'rejected'">
                                Update Order
                            </x-primary-button>
                            <p class="text-sm text-gray-500" x-show="newStatus === 'rejected'">Warning: Rejecting returns
                                stock.</p>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>