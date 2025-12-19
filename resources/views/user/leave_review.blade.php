<x-app-layout>
    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 p-8 rounded-lg shadow-xl border-t-4 border-yellow-500" x-data="{ rating: 0, hover: 0 }">
                <h2 class="text-2xl font-bold text-white mb-6">Review: {{ $order->costume->name }}</h2>
                
                <form action="{{ route('user.review.store', $order->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- Star Rating --}}
                    <div class="mb-6">
                        <label class="block text-indigo-400 font-bold mb-2">Rating</label>
                        <div class="flex space-x-2">
                            <template x-for="i in 5">
                                <button type="button" @click="rating = i" @mouseenter="hover = i" @mouseleave="hover = 0" class="focus:outline-none transition transform hover:scale-125">
                                    <svg class="h-10 w-10" :class="(hover || rating) >= i ? 'text-yellow-400' : 'text-gray-600'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <input type="hidden" name="rating" x-model="rating" required>
                    </div>

                    {{-- Comment --}}
                    <div class="mb-6">
                        <x-input-label for="comment" value="Cosmic Feedback (Optional)" class="text-indigo-400" />
                        <textarea name="comment" class="w-full bg-gray-700 border-gray-600 text-white rounded-lg focus:ring-yellow-500"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button class="bg-yellow-600">Submit Review</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>