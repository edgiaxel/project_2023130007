<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin Control Deck 🚀 <span class="text-red-400">GOD MODE ACTIVE</span>
        </h2>
    </x-slot>

    <div class="py-12" style="background-image: url('{{ asset('images/stars_bg.jpg') }}');">
        {{-- Star theme placeholder --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 bg-opacity-90 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="p-6 text-gray-100 space-y-4">
                    <p class="text-2xl font-bold">Welcome, {{ Auth::user()->name }}. Your mission control awaits.</p>
                    <p class="text-md text-gray-400">System Status: Total Costumes: 10+ | Total Renters: 5 | Pending
                        Approvals: 2</p>

                    <div class="mt-8 grid grid-cols-3 gap-6 text-center">
                        <a href="{{ route('admin.users') }}"
                            class="block p-6 bg-indigo-700 hover:bg-indigo-600 rounded-lg shadow-lg transition duration-300">
                            <span class="text-3xl font-extrabold text-white">Manage Users</span>
                        </a>
                        <a href="{{ route('admin.costumes') }}"
                            class="block p-6 bg-red-700 hover:bg-red-600 rounded-lg shadow-lg transition duration-300">
                            <span class="text-3xl font-extrabold text-white">Costume Approvals</span>
                        </a>
                        <a href="{{ route('admin.transactions') }}"
                            class="block p-6 bg-green-700 hover:bg-green-600 rounded-lg shadow-lg transition duration-300">
                            <span class="text-3xl font-extrabold text-white">Monitor Orders</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>