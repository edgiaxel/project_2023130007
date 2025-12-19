@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $isOwner = $user && $user->hasRole('owner');
    $isAdmin = $user && $user->hasRole('admin');
    $isRenter = $user && $user->hasRole('renter');
    $isUser = $user && $user->hasRole('user');

    // Notification Logic for Admin/Owner
    $pendingModCount = 0;
    if ($isAdmin || $isOwner) {
        $pendingModCount = \App\Models\ReviewModerationRequest::where('status', 'pending')->count();
    }
@endphp

<nav x-data="{ open: false }" class="bg-gray-900 border-b border-indigo-700 shadow-lg"
    style="background-image: radial-gradient(circle at center, rgba(30, 0, 70, 0.9) 0%, rgba(10, 0, 30, 1) 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('catalog') }}"
                        class="text-2xl font-extrabold text-white tracking-widest hover:text-indigo-400 transition duration-300">
                        ✨ Starium Rental
                    </a>
                </div>

                {{-- Desktop Links --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('catalog')" :active="request()->routeIs('catalog')"
                        class="text-indigo-300 hover:text-white">
                        {{ __('Cosmic Catalog') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('catalog') }}#catalog" class="text-indigo-300 hover:text-white">
                        {{ __('Collections') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('catalog') }}#about" class="text-pink-300 hover:text-white">
                        {{ __('About Starium') }}
                    </x-nav-link>
                    <x-nav-link href="#" class="text-pink-300 hover:text-white">
                        {{ __('|') }}
                    </x-nav-link>

                    @auth
                        {{-- 1. ADMIN & OWNER SHARED LINKS --}}
                        @if ($isAdmin)
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                                class="text-red-400 hover:text-white">
                                {{ __('Admin Deck') }}
                            </x-nav-link>
                        @endif
                        
                        @if ($isOwner)
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                                class="text-red-400 hover:text-white">
                                {{ __('Owner Deck') }}
                            </x-nav-link>
                        @endif

                        @if ($isAdmin || $isOwner)
                            <x-nav-link :href="route('admin.moderation.reviews')"
                                :active="request()->routeIs('admin.moderation.reviews')"
                                class="relative text-yellow-400 hover:text-white">
                                {{ __('Flagged Reviews') }}
                                @if($pendingModCount > 0)
                                    <span class="absolute -top-1 -right-4 flex h-4 w-4">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span
                                            class="relative inline-flex rounded-full h-4 w-4 bg-red-600 text-[10px] text-white items-center justify-center font-bold">
                                            {{ $pendingModCount }}
                                        </span>
                                    </span>
                                @endif
                            </x-nav-link>
                        @endif

                        {{-- 2. RENTER SPECIFIC LINKS --}}
                        @if ($isRenter)
                            <x-nav-link :href="route('renter.dashboard')" :active="request()->routeIs('renter.dashboard')"
                                class="text-pink-400 hover:text-white">
                                {{ __('Renter Panel') }}
                            </x-nav-link>
                        @endif

                        {{-- 3. CUSTOMER/USER LINKS (Visible to Renters/Users) --}}
                        @if ($isUser || $isRenter)
                            <x-nav-link :href="route('user.orders')" :active="request()->routeIs('user.orders')"
                                class="text-indigo-300 hover:text-white">
                                {{ __('My Orders') }}
                            </x-nav-link>
                            <x-nav-link :href="route('user.favorites')" :active="request()->routeIs('user.favorites')"
                                class="text-pink-300 hover:text-white">
                                {{ __('Wishlist') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Right Section: Profile --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @guest
                    <div class="space-x-4">
                        <a href="{{ route('login') }}"
                            class="text-indigo-300 hover:text-white text-sm font-semibold px-3 py-2 border border-indigo-500 rounded-md">{{ __('Log in') }}</a>
                        <a href="{{ route('register') }}"
                            class="text-white bg-pink-600 hover:bg-pink-700 text-sm font-semibold px-3 py-2 rounded-md">{{ __('Register') }}</a>
                    </div>
                @endguest

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-200 hover:text-white transition">
                                <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('default_images/default_avatar.png') }}"
                                    class="h-6 w-6 rounded-full object-cover mr-2 border border-indigo-400"
                                    onerror="this.src='{{ asset('default_images/default_avatar.png') }}'">
                                <div>{{ Auth::user()->name }}</div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Account Settings') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Log Out') }}</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            {{-- Mobile menu button --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-800 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Responsive Mobile Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-800">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('catalog')"
                :active="request()->routeIs('catalog')">{{ __('Catalog') }}</x-responsive-nav-link>
            @auth
                @if($isAdmin || $isOwner)
                    <x-responsive-nav-link :href="route('admin.dashboard')">{{ __('Admin Deck') }}</x-responsive-nav-link>
                @endif
                @if($isRenter)
                    <x-responsive-nav-link :href="route('renter.dashboard')">{{ __('Renter Panel') }}</x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('user.orders')">{{ __('My Orders') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('user.favorites')">{{ __('Wishlist ❤️') }}</x-responsive-nav-link>
            @endauth
        </div>
    </div>
</nav>