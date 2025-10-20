@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
    $isAdmin = $user && $user->hasRole('admin');
    $isRenter = $user && $user->hasRole('renter');
    $isUser = $user && $user->hasRole('user');
@endphp

<nav x-data="{ open: false }" class="bg-gray-900 border-b border-indigo-700 shadow-lg"
    style="background-image: radial-gradient(circle at center, rgba(30, 0, 70, 0.9) 0%, rgba(10, 0, 30, 1) 100%);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
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

                    {{-- NEW ANCHOR LINKS (Only visible if not authenticated or not in dashboard) --}}
                    <x-nav-link href="{{ route('catalog') }}#sale-banner" class="text-yellow-300 hover:text-white">
                        {{ __('Flash Sale') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('catalog') }}#catalog" class="text-indigo-300 hover:text-white">
                        {{ __('Collections') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('catalog') }}#about" class="text-pink-300 hover:text-white">
                        {{ __('About Starium') }}
                    </x-nav-link>

                    {{-- Auth Links --}}
                    @auth
                        @if ($isAdmin)
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                                class="text-red-300 hover:text-white">
                                {{ __('Admin Deck') }}
                            </x-nav-link>
                        @elseif ($isRenter)
                            <x-nav-link :href="route('renter.dashboard')" :active="request()->routeIs('renter.dashboard')"
                                class="text-pink-300 hover:text-white">
                                {{ __('Renter Panel') }}
                            </x-nav-link>
                            <x-nav-link :href="route('renter.orders')" :active="request()->routeIs('renter.orders')"
                                class="text-pink-300 hover:text-white">
                                {{ __('Orders') }}
                            </x-nav-link>
                        @elseif ($isUser)
                            <x-nav-link :href="route('user.orders')" :active="request()->routeIs('user.orders')"
                                class="text-indigo-300 hover:text-white">
                                {{ __('My Orders') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Right Section: Profile Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @guest
                    <div class="space-x-4">
                        <a href="{{ route('login') }}"
                            class="text-indigo-300 hover:text-white transition duration-300 text-sm font-semibold px-3 py-2 border border-indigo-500 rounded-md hover:bg-indigo-700">
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ route('register') }}"
                            class="text-white bg-pink-600 hover:bg-pink-700 transition duration-300 text-sm font-semibold px-3 py-2 rounded-md">
                            {{ __('Register') }}
                        </a>
                    </div>
                @endguest

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-200 hover:text-white focus:outline-none transition ease-in-out duration-150">
                                @php
                                    $profilePicUrl = Auth::user()->profile_picture
                                        ? asset('storage/' . Auth::user()->profile_picture)
                                        : asset('default_images/default_avatar.png');
                                @endphp

                                <img src="{{ $profilePicUrl }}" alt="{{ Auth::user()->name }} Avatar"
                                    class="h-6 w-6 rounded-full object-cover mr-2 border border-indigo-400"
                                    onerror="this.onerror=null; this.src='{{ asset('default_images/default_avatar.png') }}';">
                                <div>{{ Auth::user()->name }}</div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            {{-- Hamburger for Mobile --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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

    {{-- Responsive (Mobile) Navigation --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gray-800">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('catalog')" :active="request()->routeIs('catalog')">
                {{ __('Cosmic Catalog') }}
            </x-responsive-nav-link>

            @auth
                @if ($isAdmin)
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Admin Deck') }}
                    </x-responsive-nav-link>
                @elseif ($isRenter)
                    <x-responsive-nav-link :href="route('renter.dashboard')" :active="request()->routeIs('renter.dashboard')">
                        {{ __('Renter Panel') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('renter.orders')" :active="request()->routeIs('renter.orders')">
                        {{ __('Orders') }}
                    </x-responsive-nav-link>
                @elseif ($isUser)
                    <x-responsive-nav-link :href="route('user.orders')" :active="request()->routeIs('user.orders')">
                        {{ __('My Orders') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        {{-- Mobile Guest Links --}}
        @guest
            <div class="pt-4 pb-1 border-t border-indigo-700">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Log in') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            </div>
        @endguest

        {{-- Mobile Profile + Logout --}}
        @auth
            <div class="pt-4 pb-1 border-t border-indigo-700">
                <div class="px-4 text-indigo-200 flex items-center">

                    @php
                        $profilePicUrl = Auth::user()->profile_picture
                            ? asset('storage/' . Auth::user()->profile_picture)
                            : asset('default_images/default_avatar.png');
                    @endphp

                    <img src="{{ $profilePicUrl }}" alt="{{ Auth::user()->name }} Avatar"
                        class="h-8 w-8 rounded-full object-cover mr-3 border border-indigo-400"
                        onerror="this.onerror=null; this.src='{{ asset('default_images/default_avatar.png') }}';">
                    <div>
                        <div class="font-medium text-base">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>