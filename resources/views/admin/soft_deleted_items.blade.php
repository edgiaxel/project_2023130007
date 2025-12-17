@php
    // Prepare all trashed data for Alpine
    $trashedUsers = $deletedUsers->map(function ($u) {
        return [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $u->roles->pluck('name')->first() ?? 'user',
            'deleted_at' => $u->deleted_at->format('Y-m-d H:i')
        ];
    });

    $trashedCostumes = $deletedCostumes->map(function ($c) {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'series' => $c->series,
            'store' => $c->renter?->store?->store_name ?? 'N/A',
            'deleted_at' => $c->deleted_at->format('Y-m-d H:i')
        ];
    });

    $trashedBanners = $deletedBanners->map(function ($b) {
        return [
            'id' => $b->id,
            'title' => $b->title,
            'image' => asset('storage/' . $b->image_path),
            'deleted_at' => $b->deleted_at->format('Y-m-d H:i')
        ];
    });
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Cosmic Trash Bin (Soft Deleted Items) 🗑️
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;" x-data="{
        search: '',
        users: {{ json_encode($trashedUsers) }},
        costumes: {{ json_encode($trashedCostumes) }},
        banners: {{ json_encode($trashedBanners) }},

        // Search logic for all sections
        get filteredUsers() { return this.users.filter(u => u.name.toLowerCase().includes(this.search.toLowerCase()) || u.email.toLowerCase().includes(this.search.toLowerCase())) },
        get filteredCostumes() { return this.costumes.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.series.toLowerCase().includes(this.search.toLowerCase())) },
        get filteredBanners() { return this.banners.filter(b => b.title.toLowerCase().includes(this.search.toLowerCase())) }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-red-500">

                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <a href="{{ route('admin.users') }}" class="text-indigo-400 hover:text-white text-sm">&larr; Back to
                        Active Users</a>
                    <input type="text" x-model="search" placeholder="Search the trash..."
                        class="bg-gray-700 border-red-500 rounded-md text-white text-sm py-1 px-3 w-full md:w-64">
                </div>

                {{-- 1. USER ACCOUNTS --}}
                <h3 class="text-xl font-bold text-red-400 mb-4 border-b border-gray-700 pb-2">Trashed Users (<span
                        x-text="filteredUsers.length"></span>)</h3>
                <div class="overflow-x-auto mb-10">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700 text-gray-300 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Role</th>
                                <th class="px-4 py-3 text-left">Deleted At</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 text-gray-200">
                            <template x-for="u in filteredUsers" :key="u.id">
                                <tr>
                                    <td class="px-4 py-4">
                                        <p x-text="u.name"></p>
                                        <p class="text-xs text-gray-500" x-text="u.email"></p>
                                    </td>
                                    <td class="px-4 py-4"><span class="px-2 py-0.5 rounded-full text-xs bg-gray-600"
                                            x-text="u.role"></span></td>
                                    <td class="px-4 py-4 text-sm text-red-400" x-text="u.deleted_at"></td>
                                    <td class="px-4 py-4 text-xs space-y-1">
                                        <form :action="'{{ route('admin.users.restore', ':id') }}'.replace(':id', u.id)"
                                            method="POST">@csrf<button
                                                class="text-green-400 hover:underline">Restore</button></form>
                                        <form
                                            :action="'{{ route('admin.users.force_delete', ':id') }}'.replace(':id', u.id)"
                                            method="POST"
                                            @submit.prevent="if(confirm('ERASE ' + u.name + ' PERMANENTLY?')) $el.submit()">
                                            @csrf @method('DELETE')<button class="text-red-600 hover:underline">Perma
                                                Delete</button></form>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- 2. COSTUME LISTINGS --}}
                <h3 class="text-xl font-bold text-red-400 mb-4 border-b border-gray-700 pb-2">Trashed Costumes (<span
                        x-text="filteredCostumes.length"></span>)</h3>
                <div class="overflow-x-auto mb-10">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700 text-gray-300 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Costume</th>
                                <th class="px-4 py-3 text-left">Renter</th>
                                <th class="px-4 py-3 text-left">Deleted At</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 text-gray-200">
                            <template x-for="c in filteredCostumes" :key="c.id">
                                <tr>
                                    <td class="px-4 py-4">
                                        <p x-text="c.name"></p>
                                        <p class="text-xs text-gray-500" x-text="c.series"></p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-pink-400" x-text="c.store"></td>
                                    <td class="px-4 py-4 text-sm text-red-400" x-text="c.deleted_at"></td>
                                    <td class="px-4 py-4 text-xs space-y-1">
                                        <form
                                            :action="'{{ route('admin.costumes.restore', ':id') }}'.replace(':id', c.id)"
                                            method="POST">@csrf<button
                                                class="text-green-400 hover:underline">Restore</button></form>
                                        <form
                                            :action="'{{ route('admin.costumes.force_delete', ':id') }}'.replace(':id', c.id)"
                                            method="POST"
                                            @submit.prevent="if(confirm('ERASE ' + c.name + ' PERMANENTLY?')) $el.submit()">
                                            @csrf @method('DELETE')<button class="text-red-600 hover:underline">Perma
                                                Delete</button></form>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- 3. CATALOG BANNERS --}}
                <h3 class="text-xl font-bold text-red-400 mb-4 border-b border-gray-700 pb-2">Trashed Banners (<span
                        x-text="filteredBanners.length"></span>)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700 text-gray-300 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Banner Title</th>
                                <th class="px-4 py-3 text-left">Preview</th>
                                <th class="px-4 py-3 text-left">Deleted At</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 text-gray-200">
                            <template x-for="b in filteredBanners" :key="b.id">
                                <tr>
                                    <td class="px-4 py-4 text-white font-bold" x-text="b.title"></td>
                                    <td class="px-4 py-4">
                                        <img :src="b.image"
                                            class="h-10 w-20 object-cover rounded border border-gray-600"
                                            onerror="this.src='{{ asset('default_images/default_costume.png') }}'">
                                    </td>
                                    <td class="px-4 py-4 text-sm text-red-400" x-text="b.deleted_at"></td>
                                    <td class="px-4 py-4 text-xs space-y-1">
                                        <form
                                            :action="'{{ route('admin.banners.restore', ':id') }}'.replace(':id', b.id)"
                                            method="POST">@csrf<button
                                                class="text-green-400 hover:underline">Restore</button></form>
                                        <form
                                            :action="'{{ route('admin.banners.force_delete', ':id') }}'.replace(':id', b.id)"
                                            method="POST"
                                            @submit.prevent="if(confirm('ERASE ' + b.title + ' PERMANENTLY? File will be wiped.')) $el.submit()">
                                            @csrf @method('DELETE')<button class="text-red-600 hover:underline">Perma
                                                Delete</button></form>
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