<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Costume Approval Center 🚨
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- PENDING APPROVALS --}}
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-yellow-500">
                <h3 class="text-2xl font-bold text-yellow-400 mb-6">Costumes Awaiting Review
                    ({{ is_countable($pendingCostumes) ? $pendingCostumes->count() : 0 }})</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if (is_countable($pendingCostumes))
                        @forelse ($pendingCostumes as $costume)
                            <div class="bg-gray-700 shadow-xl rounded-lg overflow-hidden p-4 border border-yellow-500">
                                <p class="text-sm text-gray-400">Uploaded by:
                                    <span
                                        class="font-semibold text-pink-400">{{ $costume->renter->store->store_name ?? 'N/A' }}</span>
                                </p>
                                <h4 class="text-xl font-bold text-white mt-1">{{ $costume->name }}</h4>
                                <p class="text-red-400 font-semibold mt-2">Status: PENDING ADMIN APPROVAL</p>

                                @php
                                    // 1. Get the Main Image (order 0) via the relationship
                                    $mainImage = $costume->images->sortBy('order')->first();
                                    $costumeImageUrl = $mainImage
                                        ? asset('storage/' . $mainImage->image_path)
                                        : asset('default_images/default_costume.png');

                                    // 2. Generate the fallback attribute string (remains the same)
                                    $fallbackError = "this.onerror=null; this.src='" . asset('default_images/default_costume.png') . "';";
                                @endphp
                                <div class="my-3 bg-gray-600 flex items-center justify-center overflow-hidden rounded-md">
                                    <img src="{{ $costumeImageUrl }}" alt="{{ $costume->name }}"
                                        class="h-full w-auto object-cover" {{-- Inject the generated fallback string here --}}
                                        onerror="{{ $fallbackError }}">
                                </div>

                                <p class="text-gray-300 text-sm">Price: Rp
                                    {{ number_format($costume->price_per_day, 0, ',', '.') }}/day
                                </p>

                                <div class="mt-4 space-x-2">

                                    {{-- 1. APPROVE FORM (Unchanged, already works) --}}
                                    <form action="{{ route('admin.costumes.set_approval', ['costume_id' => $costume->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">Approve</button>
                                    </form>

                                    {{-- 2. REJECT FORM (FIXED: Now visible and requires confirmation) --}}
                                    <form action="{{ route('admin.costumes.set_approval', ['costume_id' => $costume->id]) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('WARNING: Are you absolutely sure you want to REJECT {{ $costume->name }}?');">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        {{-- You can add notes here if you actually implement a modal, but for now we keep it
                                        simple: --}}
                                        <input type="hidden" name="notes" value="Rejected by Admin: Failed inspection.">
                                        <button type="submit"
                                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">Reject</button>
                                    </form>


                                    <a href="{{ route('costume.detail', ['id' => $costume->id]) }}"
                                        class="text-indigo-400 hover:text-indigo-600 text-sm">View Detail</a>
                                </div>
                            </div>
                        @empty
                            <p class="col-span-3 text-gray-400">No costumes awaiting approval. Everything is running smoothly.
                            </p>
                        @endforelse
                    @endif
                                   
                </div>
            </div>
        </div>
    </div>
</x-app-layout>