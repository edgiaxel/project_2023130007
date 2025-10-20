@php
    use Illuminate\Support\Facades\Auth;
    $user = $user ?? Auth::user();
    $store = $store ?? $user->store ?? new \App\Models\RenterStore();
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Renter: Set Up Cosmic Store Profile 🏪
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 space-y-8 border-t-4 border-pink-500">
                @include('profile.partials.renter-store-form', ['user' => $user])
            </div>
        </div>
    </div>
</x-app-layout>