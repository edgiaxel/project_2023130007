<x-app-layout>
    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 rounded-lg shadow-xl p-6 border-t-4 border-red-500" x-data="{ search: '', list: {{ json_encode($requests->map(fn($r) => [
    'id' => $r->id,
    'date' => $r->created_at->format('Y-m-d'),
    'renter' => $r->renter->name,
    'reason' => $r->reason,
    'reviewer' => $r->review->user->name,
    'rating' => $r->review->rating,
    'comment' => $r->review->comment
])) }} }">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-red-400">Review Deletion Requests (Admin Panel)</h3>
                    <input type="text" x-model="search" placeholder="Search renter or reason..."
                        class="bg-gray-700 text-white rounded-lg text-sm w-64">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Request Info
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Target
                                    Review</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <template
                                x-for="r in list.filter(i => i.renter.toLowerCase().includes(search.toLowerCase()) || i.reason.toLowerCase().includes(search.toLowerCase()))"
                                :key="r.id">
                                <tr>
                                    <td class="px-4 py-4">
                                        <p class="text-pink-400 font-bold" x-text="r.renter"></p>
                                        <p class="text-xs text-gray-500" x-text="r.date"></p>
                                        <p class="text-white mt-1" x-text="r.reason"></p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="text-yellow-400" x-text="'★'.repeat(r.rating)"></p>
                                        <p class="text-xs text-gray-400" x-text="'By: ' + r.reviewer"></p>
                                        <p class="text-gray-300 italic" x-text='\"' + r.comment + '\"'></p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex gap-2" x-data="{ 
        {{-- Generate base URLs with a placeholder --}}
        approveBase: '{{ route('admin.moderation.approve', ['id' => 'REPLACE_ID']) }}',
        rejectBase: '{{ route('admin.moderation.reject', ['id' => 'REPLACE_ID']) }}'
    }">
                                            {{-- 1. APPROVE (DELETE) --}}
                                            <form :action="approveBase.replace('REPLACE_ID', r.id)" method="POST"
                                                @submit.prevent="if(confirm('ERASE THIS REVIEW FOREVER?')) $el.submit()">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 transition">
                                                    Delete Review
                                                </button>
                                            </form>

                                            {{-- 2. REJECT --}}
                                            <form :action="rejectBase.replace('REPLACE_ID', r.id)" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-gray-600 text-white px-3 py-1 rounded text-xs hover:bg-gray-500 transition">
                                                    Reject Request
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>