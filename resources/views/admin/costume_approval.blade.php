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
                    ({{ $pendingCostumes->count() }})</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse ($pendingCostumes as $costume)
                        <div class="bg-gray-700 shadow-xl rounded-lg overflow-hidden p-4 border border-yellow-500">
                            <p class="text-sm text-gray-400">Uploaded by:
                                <span
                                    class="font-semibold text-pink-400">{{ $costume->renter->store->store_name ?? 'N/A' }}</span>
                            </p>
                            <h4 class="text-xl font-bold text-white mt-1">{{ $costume->name }}</h4>
                            <p class="text-red-400 font-semibold mt-2">Status: PENDING ADMIN APPROVAL</p>

                            <div class="my-3 bg-gray-600 flex items-center justify-center overflow-hidden rounded-md">
                                <img src="{{ $costume->main_image_path ? asset('storage/' . $costume->main_image_path) : asset('default_images/default_costume.png') }}"
                                    alt="{{ $costume->name }}" class="h-full w-auto object-cover"
                                    onerror="this.onerror=null; this.src='{{ asset('default_images/default_costume.png') }}';">
                            </div>

                            <p class="text-gray-300 text-sm">Price: Rp
                                {{ number_format($costume->price_per_day, 0, ',', '.') }}/day</p>

                            <div class="mt-4 space-x-2">
                                <form action="#" method="POST" class="inline">
                                    @csrf
                                    {{-- <input type="hidden" name="status" value="approved"> --}}
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">Approve</button>
                                </form>
                                <a href="#" class="text-red-400 hover:text-red-600 text-sm">Reject w/ Notes</a>
                                <a href="{{ route('costume.detail', ['id' => $costume->id]) }}"
                                    class="text-indigo-400 hover:text-indigo-600 text-sm">View Detail</a>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-3 text-gray-400">No costumes awaiting approval. Everything is running smoothly.
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- ALL LISTED COSTUMES --}}
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-indigo-500">
                <h3 class="text-2xl font-bold text-indigo-400 mb-6">All Listed Costumes (Editable/Deletable)</h3>
            </div>
        </div>
    </div>
</x-app-layout>