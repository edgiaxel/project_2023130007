@php
use App\Models\Costume;
// Fetch costumes requiring approval
$pendingCostumes = Costume::where('is_approved', false)->with('renter')->get();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Costume Approval Center 🚨
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-yellow-500">
                <h3 class="text-lg font-bold text-gray-200 mb-4">Costumes Awaiting Cosmic Approval
                    ({{ $pendingCostumes->count() }})</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse ($pendingCostumes as $costume)
                    {{-- DYNAMIC Card for Pending Approval --}}
                    <div class="bg-gray-700 shadow-xl rounded-lg overflow-hidden p-4 border border-yellow-500">
                        <p class="text-sm text-gray-400">Uploaded by: {{ $costume->renter->name ?? 'Unknown' }}</p>
                        <h4 class="text-xl font-bold text-white mt-1">{{ $costume->name }}</h4>
                        <p class="text-red-400 font-semibold mt-2">Status: PENDING ADMIN APPROVAL</p>
                        <p class="text-gray-300 mt-2">Series: {{ $costume->series }} | Size: {{ $costume->size }} |
                            Price: Rp {{ number_format($costume->price_per_day, 0, ',', '.') }}/day</p>
                        <div class="mt-4 space-x-2">
                            <a href="#"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Approve</a>
                            <a href="#"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Reject</a>
                            <a href="{{ route('costume.detail', ['id' => $costume->id]) }}"
                                class="text-indigo-400 hover:text-indigo-600">View Detail</a>
                        </div>
                    </div>
                    @empty
                    <p class="col-span-3 text-gray-400">No costumes awaiting approval. Everything is running smoothly
                        for once.</p>
                    @endforelse
                </div>

                <h3 class="text-lg font-bold text-gray-200 mt-8 mb-4">All Approved Costumes (Soft Delete Test
                    Placeholder)</h3>
                {{-- Placeholder table for approved costumes. --}}
            </div>
        </div>
    </div>
</x-app-layout>