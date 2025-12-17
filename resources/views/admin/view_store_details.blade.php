<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Managing Store: <span class="text-pink-400">{{ $renter->store->store_name ?? 'N/A' }}</span>
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. STORE PROFILE EDIT (Unchanged) --}}
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-pink-500">
                <h3 class="text-2xl font-bold text-pink-400 mb-6">Store Profile & Details (Owner: {{ $renter->name }})
                </h3>

                {{-- FORM FOR ADMIN STORE DETAILS UPDATE (Already fixed in previous step to point to admin.stores.update_details) --}}
                <form method="post" action="{{ route('admin.stores.update_details', $renter->id) }}"
                    enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')

                    {{-- Store Name --}}
                    <div>
                        <x-input-label for="store_name" :value="__('Store Name')" class="text-pink-400" />
                        <x-text-input id="store_name" name="store_name" type="text"
                            class="mt-1 block w-full bg-gray-700 border-gray-600 text-white" :value="old('store_name', $renter->store->store_name ?? $renter->name . ' Shop')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('store_name')" />
                    </div>

                    {{-- Store Logo --}}
                    <div>
                        <x-input-label for="store_logo" :value="__('Store Logo')" class="text-pink-400" />
                        <input id="store_logo" name="store_logo" type="file"     
                            class="block mt-1 w-full text-sm text-gray-200 border-gray-600 rounded-lg cursor-pointer bg-gray-700 focus:outline-none" />
                        <x-input-error class="mt-2" :messages="$errors->get('store_logo')" />
                        @if($renter->store && $renter->store->store_logo_path)
                            <p class="text-xs text-gray-500 mt-1">
                                Current File: {{ basename($renter->store->store_logo_path) }}
                            </p>
                        @endif
                    </div>

                    {{-- Store Description --}}
                    <div>
                        <x-input-label for="description" :value="__('Store Description (Public)')"
                            class="text-pink-400" />
                        <textarea id="description" name="description"
                            class="mt-1 block w-full border-gray-600 bg-gray-700 text-white focus:border-pink-500 focus:ring-pink-500 rounded-md shadow-sm">{{ old('description', $renter->store->description ?? 'We rent the best costumes in the entire solar system!') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button class="bg-red-600 hover:bg-red-700">
                            {{ __('Save Store Details (Admin Override)') }}
                        </x-primary-button>
                    </div>
                </form>

                <div class="mt-8 pt-4 border-t border-gray-700">
                    <p class="text-sm text-gray-400">
                        To manage the **Owner's personal details** (Name, Email, Phone, Address), please navigate to the
                        main <a href="{{ route('admin.users') }}" class="text-indigo-400 hover:text-indigo-600">Manage
                            Users</a> page.
                    </p>
                </div>
            </div>

            {{-- 2. COSTUME MANAGEMENT TABLE --}}
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-indigo-500">
                <h3 class="text-2xl font-bold text-indigo-400 mb-6">Costumes Listed by This Store 
                    ({{ $renter->costumes->count() }})  <a href="{{ route('admin.soft_delete.index') }}" class="ml-4 text-sm text-red-400 hover:text-red-300 font-semibold">(View Trash Bin)</a></h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Costume</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Series</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Total Sales
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Approval
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            @forelse ($renter->costumes as $costume)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $costume->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $costume->series }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-indigo-400">
                                        {{ $costume->stock }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ $costume->orders_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{-- 💥 FIX: Display status based on string value --}}
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-white 
                                            @if ($costume->status === 'approved') bg-green-700 
                                            @elseif ($costume->status === 'rejected') bg-red-700 
                                            @else bg-yellow-700 @endif">
                                            {{ strtoupper($costume->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        {{-- FIX: Link to Renter's Edit form (Admin/Owner can access if they have 'costume:edit-all') --}}
                                        <a href="{{ route('renter.costumes.edit', $costume->id) }}"
                                            class="text-indigo-400 hover:text-indigo-600">Edit</a>

                                        {{-- FIX: Implement SOFT DELETE form (Admin/Owner can access if they have 'costume:delete-all') --}}
                                        <form action="{{ route('renter.costumes.delete', $costume->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('WARNING: Are you absolutely sure you want to SOFT DELETE this costume ({{ $costume->name }})? This is an Admin action.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600">Delete
                                                (Soft)</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-400">This renter has no costumes
                                        listed.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>