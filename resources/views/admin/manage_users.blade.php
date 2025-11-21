@php
    use App\Models\User;
    use Illuminate\Support\Facades\Auth;
    // Fetch all users who are NOT the current Admin
    $users = User::where('id', '!=', Auth::id())->with('roles')->get();

    // NEW: Check if the current user is the owner
    $isOwner = Auth::user()->hasRole('owner'); 
    $availableRoles = ['user', 'renter', 'admin']; // Standard admin can only promote up to admin
    if ($isOwner) {
        $availableRoles[] = 'owner'; // Owner can promote to owner
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            Admin: Manage Platform Users 🛠️
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border-t-4 border-red-500">
                <h3 class="text-lg font-bold text-gray-200 mb-4">Platform Accounts List</h3>

                @if ($isOwner)
                <p class="mb-4 text-yellow-400 font-semibold">NOTE: Since you are the Owner, you see the role dropdown and an extra "Edit Permissions" link.</p>
                @endif
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                {{-- ... (Name, Email, Role headers remain the same) ... --}}
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700 text-gray-200">
                            @foreach ($users as $userItem)
                                @php
                                    $role = $userItem->roles->pluck('name')->first() ?? 'Unassigned';
                                    $color = [
                                        'owner' => 'bg-green-600', // NEW
                                        'admin' => 'bg-red-600',
                                        'renter' => 'bg-pink-600',
                                        'user' => 'bg-blue-600',
                                        'Unassigned' => 'bg-gray-500'
                                    ][$role] ?? 'bg-gray-500';
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $userItem->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $userItem->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }} text-white">{{ ucfirst($role) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <form action="{{ route('admin.users.updateRole', $userItem->id) }}"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <select name="role_name" onchange="this.form.submit()"
                                                    class="bg-gray-700 border-indigo-500 rounded-md text-white text-sm py-1">
                                                    @foreach ($availableRoles as $availableRole)
                                                        {{-- Owner can see and use all roles in the dropdown --}}
                                                        @if ($isOwner || $availableRole !== 'owner') 
                                                            <option value="{{ $availableRole }}" @selected($availableRole === $role)>
                                                                {{ ucfirst($availableRole) }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </form>
                                        </div>
                                        
                                        {{-- Profile Edit Link (Admin/Owner feature) --}}
                                        <a href="{{ route('admin.users.edit', $userItem->id) }}"
                                            class="text-indigo-400 hover:text-indigo-600 text-xs block">Edit Profile</a>
                                        
                                        {{-- OWNER ONLY: Direct Permission Edit Link --}}
                                        @if ($isOwner)
                                            <a href="{{ route('owner.users.permissions.edit', $userItem->id) }}"
                                                class="text-yellow-400 hover:text-yellow-600 text-xs block font-bold mt-1">
                                                Edit Permissions
                                            </a>
                                        @endif
                                        
                                        <a href="#" class="text-red-400 hover:text-red-600 text-xs block">Deactivate</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>