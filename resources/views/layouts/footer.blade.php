<footer class="bg-gray-800 border-t border-indigo-700 mt-12 pt-10 pb-6 text-gray-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-2 gap-8">
            {{-- 1. About Starium / Contact --}}
            <div>
                <h4 class="text-xl font-bold text-white mb-4">Starium Rental</h4>
                <p class="text-sm mb-3">The premier space for costume rentals, powered by Laravel and pure cosmic energy.</p>
                <p class="text-sm">
                    Email : support@starium.test <br>
                    Address : Andromeda Galaxy Hub, Earth Sector
                </p>
            </div>

            {{-- 2. Quick Links --}}
            <div>
                <h4 class="text-xl font-bold text-white mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('catalog') }}#sale-banner" class="hover:text-pink-400 transition">Flash Sale Deals</a></li>
                    <li><a href="{{ route('catalog') }}#about" class="hover:text-pink-400 transition">Our Mission (About)</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-pink-400 transition">Log In / Register</a></li>
                    <li><p class="mt-2 text-xs">Powered by Laravel, PHP, and Node.js.</p></li>  
            </div>
        </div>

        <div class="mt-10 border-t border-gray-700 pt-6 text-center text-sm">
            &copy; {{ date('Y') }} Starium Cosplay Rental. All Rights Reserved.
        </div>
    </div>
</footer>