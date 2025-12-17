@php
    use App\Models\User;
    use Illuminate\Support\Facades\Auth;

    // Fetch all users who are NOT the current Admin
    // 💥 Important: We map the role name to a top-level property for Alpine to sort easily
    $users = User::where('id', '!=', Auth::id())->with('roles')->get()->map(function ($u) {
        $u->role_name = $u->roles->pluck('name')->first() ?? 'user';
        return $u;
    });

    $isOwner = Auth::user()->hasRole('owner');
    $availableRoles = ['user', 'renter', 'admin'];
    if ($isOwner) {
        $availableRoles[] = 'owner';
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Manage Platform Users 🛠️
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        {{-- Initialize Alpine Object --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{
            users: {{ json_encode($users) }},
            filterRole: '',
            sortColumn: 'name',
            sortDirection: 'asc',
            
            get filteredUsers() {
                let filtered = this.users.filter(u => 
                    !this.filterRole || u.role_name === this.filterRole
                );
                
                return filtered.sort((a, b) => {
                    let aVal = (a[this.sortColumn] || '').toString().toLowerCase();
                    let bVal = (b[this.sortColumn] || '').toString().toLowerCase();
                    
                    const comparison = aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
                    return this.sortDirection === 'asc' ? comparison : -comparison;
                });
            },

            sort(column) {
                if (this.sortColumn === column) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortColumn = column;
                    this.sortDirection = 'asc';
                }
            }
        }">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-red-500">

                {{-- Toolbar: Filter and Links --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <h3 class="text-lg font-bold text-gray-200">
                        Platform Accounts List
                        <a href="{{ route('admin.soft_delete.index') }}"
                            class="ml-4 text-sm text-red-400 hover:text-red-300 font-semibold">(View Trash Bin)</a>
                    </h3>

                    <div class="flex items-center space-x-3">
                        <label class="text-gray-400 text-sm">Filter by Role:</label>
                        <select x-model="filterRole"
                            class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-1">
                            <option value="">ALL ROLES</option>
                            @foreach($availableRoles as $r)
                                <option value="{{ $r }}">{{ strtoupper($r) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($isOwner)
                    <p class="mb-4 text-yellow-400 font-semibold text-xs italic">Owner Clearance: Permissions editing
                        enabled.</p>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                {{-- Clickable Headers for Sorting --}}
                                <th @click="sort('name')"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Name <span x-show="sortColumn === 'name'"
                                        x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th @click="sort('email')"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Email <span x-show="sortColumn === 'email'"
                                        x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th @click="sort('role_name')"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-600 transition">
                                    Role <span x-show="sortColumn === 'role_name'"
                                        x-text="sortDirection === 'asc' ? '▲' : '▼'"></span>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            <template x-for="userItem in filteredUsers" :key="userItem.id">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="userItem.name"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="userItem.email"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full text-white"
                                            :class="{
                                                'bg-green-600': userItem.role_name === 'owner',
                                                'bg-red-600': userItem.role_name === 'admin',
                                                'bg-pink-600': userItem.role_name === 'renter',
                                                'bg-blue-600': userItem.role_name === 'user'
                                              }"
                                            x-text="userItem.role_name.charAt(0).toUpperCase() + userItem.role_name.slice(1)">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-y-1">
                                        <div class="flex items-center space-x-2">
                                            {{-- Use standard PHP form for the update but route dynamically via Alpine
                                            --}}
                                            @php $roleRouteBase = route('admin.users.updateRole', ['user_id' => 'USER_ID']); @endphp
                                            <form :action="'{{ $roleRouteBase }}'.replace('USER_ID', userItem.id)"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <select name="role_name" onchange="this.form.submit()"
                                                    class="bg-gray-700 border-indigo-500 rounded-md text-white text-xs py-0.5">
                                                    @foreach ($availableRoles as $availableRole)
                                                        @if ($isOwner || $availableRole !== 'owner')
                                                            <option :value="'{{ $availableRole }}'"
                                                                :selected="userItem.role_name === '{{ $availableRole }}'">
                                                                {{ ucfirst($availableRole) }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </form>
                                        </div>

                                        @php $editRouteBase = route('admin.users.edit', ['user_id' => 'USER_ID']); @endphp
                                        <a :href="'{{ $editRouteBase }}'.replace('USER_ID', userItem.id)"
                                            class="text-indigo-400 hover:text-indigo-600 text-xs block">Edit Profile</a>

                                        @if ($isOwner)
                                            @php $permRouteBase = route('owner.users.permissions.edit', ['user_id' => 'USER_ID']); @endphp
                                            <a :href="'{{ $permRouteBase }}'.replace('USER_ID', userItem.id)"
                                                class="text-yellow-400 hover:text-yellow-600 text-xs block font-bold">Permissions</a>
                                        @endif

                                        @php $deleteRouteBase = route('admin.users.soft_delete', ['user_id' => 'USER_ID']); @endphp
                                        <form :action="'{{ $deleteRouteBase }}'.replace('USER_ID', userItem.id)"
                                            method="POST" class="inline-block"
                                            @submit.prevent="if (confirm('WARNING: Soft delete ' + userItem.name + '?')) $el.submit()">
                                            @csrf
                                            <button type="submit"
                                                class="text-red-400 hover:text-red-600 text-xs block">Soft
                                                Delete</button>
                                        </form>
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