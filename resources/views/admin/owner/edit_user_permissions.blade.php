<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            OWNER DECK: Editing Permissions for <span class="text-yellow-400">{{ $userToEdit->name }}</span> (Current
            Role: <span
                class="font-bold text-red-500">{{ $userToEdit->roles->pluck('name')->first() ?? 'None' }}</span>)
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #0d0d1f;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-gray-800 shadow sm:rounded-lg border-t-4 border-yellow-500">
                <header class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-100">
                        Role & Direct Permission Override
                    </h2>
                    <p class="mt-1 text-sm text-gray-400">
                        WARNING: You are directly editing roles and permissions. This bypasses the default logic.
                    </p>
                </header>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-900/50 rounded-lg">
                        <ul class="text-sm text-red-400 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('owner.users.permissions.sync', $userToEdit->id) }}"
                    class="mt-6 space-y-8">
                    @csrf

                    {{-- ROLE SELECTION (Standard/Default Permissions) --}}
                    <div>
                        <x-input-label for="role_name" value="1. Assign Primary Role (Default Permissions)"
                            class="text-yellow-400 text-lg mb-2" />
                        <select id="role_name" name="role_name" required
                            class="mt-1 block w-full bg-gray-700 border-yellow-600 text-white rounded-md shadow-sm">
                            {{-- Owner can assign ANY role, including owner itself --}}
                            @foreach (Spatie\Permission\Models\Role::all() as $roleOption)
                                <option value="{{ $roleOption->name }}" @selected($userToEdit->hasRole($roleOption->name))>
                                    {{ ucfirst($roleOption->name) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('role_name')" />
                    </div>

                    {{-- DIRECT PERMISSION OVERRIDE (Fine-Grained Control) --}}
                    <div>
                        <x-input-label for="permissions"
                            value="2. Direct Permission Overrides (ADDITIONAL or REMOVE from role)"
                            class="text-yellow-400 text-lg mb-2" />
                        <p class="text-sm text-gray-500 mb-4">Check a box to grant this user the permission directly ,
                            regardless of their role.</p>

                        <div
                            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-96 overflow-y-auto p-4 border border-gray-700 rounded-lg bg-gray-700">
                            @foreach ($allPermissions as $permission)
                                                    @php
                                                        // Check if the user has this permission directly assigned
                                                        $hasDirectPermission = $userToEdit->hasDirectPermission($permission->name);

                                                        // Check if the permission is granted via their current role(s)
                                                        // NOTE: $currentPermissions contains ALL permissions (direct + via role)
                                                        $hasViaRole = in_array($permission->name, $currentPermissions) && !$hasDirectPermission;
                                                    @endphp

                                                    <label class="flex items-center text-sm text-gray-300 space-x-2 p-2 rounded-md transition duration-150
                                    {{ $hasDirectPermission ? 'bg-indigo-900 border border-indigo-500'
                                : ($hasViaRole ? 'bg-pink-900/50' : 'hover:bg-gray-600') }}">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                            class="rounded text-yellow-500 bg-gray-800 border-gray-600"
                                                            @checked(in_array($permission->name, $currentPermissions))>

                                                        <span class="truncate" title="{{ $permission->name }}">
                                                            {{ $permission->name }}
                                                        </span>

                                                        @if ($hasDirectPermission)
                                                            <span class="text-xs text-yellow-500 ml-auto">(Direct)</span>
                                                        @elseif ($hasViaRole)
                                                            <span class="text-xs text-pink-400 ml-auto">(Via Role)</span>
                                                        @endif
                                                    </label>
                            @endforeach
                        </div>

                        <x-input-error class="mt-2" :messages="$errors->get('permissions')" />
                    </div>


                    <div class="flex items-center gap-4">
                        <x-primary-button
                            class="bg-red-600 hover:bg-red-700">{{ __('Save Changes') }}</x-primary-button>
                        <a href="{{ route('admin.users') }}" class="text-gray-400 hover:text-white text-sm">Cancel and
                            Return</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>