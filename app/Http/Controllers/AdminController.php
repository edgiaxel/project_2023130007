<?php

namespace App\Http\Controllers;

use App\Models\Costume;
use App\Models\Order;
use App\Models\User;
use App\Models\GlobalDiscount;
use App\Models\CatalogBanner;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role; // ADD THIS
use Spatie\Permission\Models\Permission; // ADD THIS

class AdminController extends Controller
{
    public function dashboard(): View
    {
        // 1. GLOBAL KPIs
        $totalCostumes = Costume::where('is_approved', true)->count();
        $totalRenters = User::role('renter')->count();
        $totalUsers = User::role('user')->count();
        $pendingApprovals = Costume::where('is_approved', false)->count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        // 2. RENTER ANALYTICS SUMMARY (for chart/table data)
        $renterSummaries = User::role('renter')->withCount('costumes')->get()->map(function ($renter) {
            $revenue = Order::whereHas('costume', fn($q) => $q->where('user_id', $renter->id))
                ->where('status', 'completed')
                ->sum('total_price');
            $renter->revenue = $revenue;
            $renter->top_costume = $renter->costumes->first()->name ?? 'N/A';
            return $renter;
        })->sortByDesc('revenue')->take(5);

        // 3. Global Discount Status
        $globalDiscount = GlobalDiscount::first() ?? new GlobalDiscount(['rate' => 0, 'is_active' => false]);

        return view('admin.dashboard', compact(
            'totalCostumes',
            'totalRenters',
            'totalUsers',
            'pendingApprovals',
            'totalRevenue',
            'renterSummaries',
            'globalDiscount'
        ));
    }

    /**
     * Detailed Analytics view for a specific Renter.
     */
    public function viewRenterAnalytics(int $userId): View
    {
        $renter = User::findOrFail($userId);
        $store = $renter->store;

        // 1. Costumes and Sales Count
        $costumes = $renter->costumes()->withCount(['orders' => fn($q) => $q->whereIn('status', ['completed', 'borrowed'])])->get();
        $totalRevenue = $costumes->sum(fn($c) => $c->orders_count * $c->price_per_day); // Simplified revenue estimate

        // 2. Monthly Revenue Data
        $monthlyData = [
            (object) ['month' => 'Jan', 'sales' => 500000],
            (object) ['month' => 'Feb', 'sales' => 750000],
            (object) ['month' => 'Mar', 'sales' => 600000],
            (object) ['month' => 'Apr', 'sales' => 1200000],
        ];

        return view('admin.renter_analytics', compact('renter', 'store', 'costumes', 'monthlyData', 'totalRevenue'));
    }
    public function approvalQueue(): View
    {
        $pendingCostumes = Costume::where('is_approved', false)
            ->with('renter.store')
            ->orderBy('created_at', 'asc')
            ->get();

        $allCostumes = Costume::with('renter.store')->get();

        return view('admin.costume_approval', compact('pendingCostumes', 'allCostumes'));
    }

    public function manageUsers(): View
    {
        // FIX: Include all roles (Owner, Admin, Renter, User) for Owner/Admin oversight
        $users = User::whereHas('roles', fn($q) => $q->whereIn('name', ['owner', 'admin', 'renter', 'user']))->with('roles', 'store')->get();
        // Filter out the current user for the display table.
        $users = $users->filter(fn($user) => $user->id !== Auth::id());

        return view('admin.manage_users', compact('users'));
    }

    public function monitorTransactions(): View
    {
        $orders = Order::with(['costume.renter', 'user'])->latest()->get();

        return view('admin.monitor_transactions', compact('orders'));
    }

    public function manageCostumes(): View
    {
        $costumes = Costume::with('renter.store')->orderBy('created_at', 'desc')->get();
        return view('admin.manage_costumes', compact('costumes'));
    }

    public function manageDiscounts(): View
    {
        $globalDiscount = GlobalDiscount::first() ?? new GlobalDiscount(['rate' => 0, 'is_active' => false]);
        return view('admin.manage_discounts', compact('globalDiscount'));
    }
    public function setGlobalDiscount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        GlobalDiscount::updateOrCreate(
            ['id' => 1],
            [
                'rate' => $validated['rate'] / 100,
                'is_active' => $request->has('is_active'),
            ]
        );

        return redirect()->route('admin.discounts.manage')->with('status', 'Global flash sale updated.');
    }

    public function manageBanners(): View
    {
        $banners = CatalogBanner::orderBy('order')->get();
        return view('admin.manage_banners', compact('banners'));
    }

    /**
     * Handle Banner Image and Title updates.
     */
    public function updateBanner(Request $request, int $bannerId): RedirectResponse
    {
        $banner = CatalogBanner::findOrFail($bannerId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg|max:3048',
        ]);

        $imagePath = $banner->image_path;

        if ($request->hasFile('image_file')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image_file')->store('banners', 'public');
        }

        $banner->update([
            'title' => $validated['title'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('admin.banners.manage')->with('status', 'Banner updated successfully!');
    }

    /**
     * Show list of all Renters/Stores for management.
     */
    public function manageRenterStores(): View
    {
        $renters = User::role('renter')->with('store')->withCount('costumes')->get();
        return view('admin.manage_stores', compact('renters'));
    }

    /**
     * Show detailed view of a single Renter's Store, including their costumes.
     */
    public function viewRenterStoreDetails(int $userId): View
    {
        $renter = User::role('renter')
            ->where('id', $userId)
            ->with(['store', 'costumes' => fn($q) => $q->withCount('orders')])
            ->firstOrFail();

        return view('admin.view_store_details', compact('renter'));
    }

    /**
     * Show the profile edit form for a specific user (accessible only by Admin/Owner).
     */
    public function editUser(int $userId): View
    {
        $user = User::with('store')->findOrFail($userId);
        $isAdminView = true;

        return view('admin.edit_user_profile', compact('user', 'isAdminView'));
    }

    /**
     * Handle updating another user's profile information (accessible only by Admin/Owner).
     */
    public function updateUser(Request $request, int $userId): RedirectResponse
    {
        // 1. Fetch user and validate input
        $user = User::findOrFail($userId);
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // 2. Handle Profile Picture Upload (reusing logic from ProfileController)
        $profilePicPath = $user->profile_picture;
        if ($request->hasFile('profile_picture')) {
            if ($profilePicPath && Storage::disk('public')->exists($profilePicPath)) {
                Storage::disk('public')->delete($profilePicPath);
            }
            $profilePicPath = $request->file('profile_picture')->store('user_profiles', 'public');
        }
        $validatedData['profile_picture'] = $profilePicPath;

        // 3. Update the user
        $user->update($validatedData);

        return redirect()->route('admin.users')->with('status', $user->name . ' profile updated successfully.');
    }

    /**
     * Sets the approval status of a costume (Admin action).
     * The costume status should be set, saved, and the page redirected.
     */
    public function setCostumeApproval(Request $request, int $costume_id): RedirectResponse
    {
        $costume = Costume::findOrFail($costume_id);

        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['action'] === 'approve') {
            $costume->is_approved = true;
            $message = 'Costume "' . $costume->name . '" has been **APPROVED** and is now live!';
        } else {
            // Rejection logic: Sets is_approved to FALSE.
            $costume->is_approved = false;
            $message = 'Costume "' . $costume->name . '" has been **REJECTED**. Notes: ' . ($validated['notes'] ?? 'None provided.');
        }

        $costume->save();

        // The redirect location is correct for showing the status message.
        return Redirect::route('admin.costumes.approval')->with('status', $message);
    }

    /**
     * Updates the role of a user (Admin/Owner action).
     */
    public function updateRole(Request $request, int $userId): RedirectResponse
    {
        $userToUpdate = User::findOrFail($userId);
        $currentUser = Auth::user();

        // 1. Core Security Checks
        if ($currentUser->id === $userToUpdate->id) {
            return Redirect::back()->withErrors(['role_error' => 'ERROR: You cannot modify your own role while logged in.']);
        }

        // **NEW CRITICAL CHECK: OWNER PERMISSION**
        if (!$currentUser->hasPermissionTo('user:manage-roles')) {
            if ($userToUpdate->hasAnyRole(['admin', 'owner']) || $request->input('role_name') === 'owner' || $request->input('role_name') === 'admin') {
                return Redirect::back()->withErrors(['role_error' => 'ACCESS DENIED: You lack the cosmic clearance to manage this role. Only the true Owner can touch Admin/Owner accounts.']);
            }
        }

        $validated = $request->validate([
            'role_name' => ['required', Rule::in(Role::pluck('name')->toArray())],
        ]);

        $newRole = $validated['role_name'];

        // 2. Perform Role Sync
        $userToUpdate->syncRoles([$newRole]);

        return Redirect::route('admin.users')->with('status', $userToUpdate->name . ' role updated to ' . ucfirst($newRole) . '.');
    }

    // --- OWNER SPECIFIC METHODS ---

    public function editUserPermissions(int $userId): View
    {
        $userToEdit = User::with('roles', 'permissions')->findOrFail($userId);
        $roles = Role::all();
        $allPermissions = Permission::all()->sortBy('name');
        $currentPermissions = $userToEdit->getAllPermissions()->pluck('name')->toArray();

        return view('admin.owner.edit_user_permissions', compact('userToEdit', 'roles', 'allPermissions', 'currentPermissions'));
    }

    public function syncUserPermissions(Request $request, int $userId): RedirectResponse
    {
        if (!Auth::user()->hasRole('owner')) {
            abort(403, 'Permission denied. You are not the chosen one.');
        }

        $userToUpdate = User::findOrFail($userId);
        if (Auth::id() === $userToUpdate->id) {
            return Redirect::back()->withErrors(['error' => 'You cannot modify your own roles/permissions while logged in.']);
        }

        $validated = $request->validate([
            'role_name' => ['required', Rule::in(Role::pluck('name')->toArray())],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $userToUpdate->syncRoles([$validated['role_name']]);
        $userToUpdate->syncPermissions($validated['permissions'] ?? []);

        return Redirect::route('admin.users')->with('status', $userToUpdate->name . ' roles and permissions have been brutally overridden.');
    }

    /**
     * Handle updating another Renter's Store details (Admin/Owner action).
     */
    public function updateRenterStoreDetails(Request $request, int $userId): RedirectResponse
    {
        $renter = User::role('renter')->with('store')->findOrFail($userId);

        $validatedStore = $request->validate([
            'store_name' => [
                'required',
                'string',
                'max:255',
                // Ignore the current user's ID for the unique check
                Rule::unique(\App\Models\RenterStore::class)->ignore($userId, 'user_id')
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'store_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $store = $renter->store ?? new \App\Models\RenterStore(['user_id' => $renter->id]);

        $logoPath = $store->store_logo_path;
        if ($request->hasFile('store_logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('store_logo')->store('store_logos', 'public');
        }

        $store->updateOrCreate(
            ['user_id' => $renter->id],
            [
                'store_name' => $validatedStore['store_name'],
                'description' => $validatedStore['description'],
                'store_logo_path' => $logoPath,
            ]
        );

        return Redirect::route('admin.stores.view', $renter->id)->with('status', 'Store profile for ' . $renter->name . ' updated successfully by Admin.');
    }
}