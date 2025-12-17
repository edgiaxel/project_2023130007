<?php

namespace App\Http\Controllers;

use App\Models\Costume;
use App\Models\Order;
use App\Models\User;
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
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\OrderController; // Ensure import is present

class AdminController extends Controller
{// Fix all methods that use Auth::user()->hasRole or Auth::user()->hasPermissionTo

    /** @var User $currentUser */ // Add this DocBlock for clarity/IDE fix
    // Example: public function updateRole(Request $request, int $userId): RedirectResponse
    // Find Auth::user() and add the explicit User model reference:
    public function dashboard(): View
    {
        OrderController::checkAndExpireOverdueOrders();
        // 1. GLOBAL KPIs
        // NEW: Use the new 'status' column and the string 'approved'
        $totalCostumes = Costume::where('status', 'approved')->count();

        $totalRenters = User::role('renter')->count();
        $totalUsers = User::role('user')->count();

        // NEW: Use the new 'status' column and the string 'pending'
        $pendingApprovals = Costume::where('status', 'pending')->count();

        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        // 2. RENTER ANALYTICS SUMMARY (for chart/table data)
        $renterSummaries = User::role('renter')->get()->map(function ($renter) {
            $costumeQuery = $renter->costumes(); // Get base query

            // Get costume counts by status
            $renter->active_costumes = (clone $costumeQuery)->where('status', 'approved')->count();
            $renter->pending_costumes = (clone $costumeQuery)->where('status', 'pending')->count();
            $renter->rejected_costumes = (clone $costumeQuery)->where('status', 'rejected')->count();
            $renter->total_costumes = $renter->active_costumes + $renter->pending_costumes + $renter->rejected_costumes; // Grand total

            $revenue = Order::whereHas('costume', fn($q) => $q->where('user_id', $renter->id))->where('status', 'completed')->sum('total_price');
            $renter->revenue = $revenue;

            // Fetch the top costume from the approved list if possible
            $renter->top_costume = (clone $costumeQuery)->where('status', 'approved')->first()->name ?? 'N/A';
            return $renter;
        })->sortByDesc('revenue')->take(5);

        return view('admin.dashboard', compact(
            'totalCostumes',
            'totalRenters',
            'totalUsers',
            'pendingApprovals',
            'totalRevenue',
            'renterSummaries'
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
        // 💥 FIX: Query by the new 'status' column string value
        $pendingCostumes = Costume::where('status', 'pending')
            ->with(['renter.store', 'images']) // 💥 ADDED IMAGES
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
    /**
     * Sets the approval status of a costume (Admin action).
     */
    public function setCostumeApproval(Request $request, int $costume_id): RedirectResponse
    {
        $costume = Costume::findOrFail($costume_id);

        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // 💥 FIX: Set 'status' string based on action
        if ($validated['action'] === 'approve') {
            $costume->status = 'approved';
            $message = 'Costume "' . $costume->name . '" has been **APPROVED** and is now live!';
        } else {
            // Rejection logic: Sets status to 'rejected'.
            $costume->status = 'rejected';
            $message = 'Costume "' . $costume->name . '" has been **REJECTED**. Notes: ' . ($validated['notes'] ?? 'None provided.');
        }

        $costume->save();
        return Redirect::route('admin.costumes.approval')->with('status', $message);
    }

    /**
     * Updates the role of a user (Admin/Owner action).
     */
    public function updateRole(Request $request, int $userId): RedirectResponse
    {
        $userToUpdate = User::findOrFail($userId);
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        // 1. Core Security Checks
        if ($currentUser->id === $userToUpdate->id) {
            return Redirect::back()->withErrors(['role_error' => 'ERROR: You cannot modify your own role while logged in.']);
        }

        // **NEW CRITICAL CHECK: OWNER PERMISSION** (Uses hasPermissionTo method)
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
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('owner')) {
            abort(403, 'Permission denied. Only the ultimate cosmic being can edit permissions.');
        }

        $userToEdit = User::with('roles', 'permissions')->findOrFail($userId);
        $roles = Role::all();
        $allPermissions = Permission::all()->sortBy('name');
        $currentPermissions = $userToEdit->getAllPermissions()->pluck('name')->toArray();

        return view('admin.owner.edit_user_permissions', compact('userToEdit', 'roles', 'allPermissions', 'currentPermissions'));
    }

    public function syncUserPermissions(Request $request, int $userId): RedirectResponse
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();

        if (!$currentUser->hasRole('owner')) {
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

    /**
     * Toggles the active status of a Renter Store and handles related records.
     * @param int $userId The ID of the Renter (User) whose store is being toggled.
     */
    public function toggleStoreStatus(int $userId): RedirectResponse
    {
        $renter = User::role('renter')
            ->with(['store', 'costumes.orders'])
            ->findOrFail($userId);
        $store = $renter->store;

        if (!$store) {
            return Redirect::back()->withErrors(['store_error' => 'Store profile missing for this renter.']);
        }

        $newStatus = !$store->is_active;
        $store->is_active = $newStatus;
        $store->save();

        $message = $newStatus ? 'activated' : 'deactivated';
        $statusUpdateMessage = '';

        // Get all ongoing orders for this renter's costumes (unchanged logic)
        $orders = Order::whereIn('costume_id', $renter->costumes->pluck('id'))
            ->whereIn('status', ['waiting', 'confirmed', 'borrowed', 'returned']);

        if (!$newStatus) {
            // --- DEACTIVATION LOGIC ---
            $orders->update(['status' => 'rejected']);

            $renter->costumes->each(function ($costume) {
                // Only save original status AND change if currently approved
                if ($costume->status === 'approved') {
                    $costume->original_status = 'approved'; // Record the original status
                    $costume->status = 'pending';          // Set current status to pending (unavailable)
                    $costume->save();
                } else {
                    // For already pending/rejected items, just clear the original_status flag
                    $costume->original_status = null;
                    $costume->save();
                }
            });

            $statusUpdateMessage = "All approved listings moved to pending/hidden. Active orders rejected.";

        } else {
            // --- ACTIVATION LOGIC ---

            $restoredCount = 0;
            $renter->costumes->each(function ($costume) use (&$restoredCount) {
                // Restore only if the costume was ACTIVE before deactivation
                if ($costume->original_status === 'approved') {
                    $costume->status = 'approved'; // Set status back to live
                    $costume->original_status = null; // Clear the flag
                    $costume->save();
                    $restoredCount++;
                }
            });

            $statusUpdateMessage = "Restored **{$restoredCount}** approved listings. Store open for new business.";
        }


        return Redirect::route('admin.stores.manage')->with('status', "Store **{$store->store_name}** has been {$message}. {$statusUpdateMessage}");
    }

    /**
     * Shows a list of all soft-deleted items (Users, Costumes).
     */
    public function softDeletedItems(): View
    {
        // Fetch all soft-deleted Users (except the current user)
        $deletedUsers = User::onlyTrashed()->with('roles', 'store')->where('id', '!=', Auth::id())->get();

        // Fetch all soft-deleted Costumes
        $deletedCostumes = Costume::onlyTrashed()->with('renter.store')->get();

        // Optional: Fetch deleted orders if needed (we'll focus on Users/Costumes for now)
        // $deletedOrders = Order::onlyTrashed()->get();
        // 💥 NEW: 3. Fetch Trashed Banners
        $deletedBanners = \App\Models\CatalogBanner::onlyTrashed()->get();

        return view('admin.soft_deleted_items', compact('deletedUsers', 'deletedCostumes', 'deletedBanners'));
    }

    /**
     * Restores a soft-deleted user.
     */
    public function restoreUser(int $userId): RedirectResponse
    {
        // Use withTrashed() to find deleted users
        $user = User::withTrashed()->findOrFail($userId);
        $user->restore();

        // Restore related RenterStore if it exists
        if ($user->store) {
            $user->store->restore(); // Assuming RenterStore supports soft deletes if needed, but not implemented above.
        }

        return Redirect::route('admin.soft_delete.index')->with('status', 'User ' . $user->name . ' has been restored from the trash.');
    }

    /**
     * Permanently deletes a soft-deleted user and all related data (forceDelete).
     */
    public function forceDeleteUser(int $userId): RedirectResponse
    {
        // Use withTrashed() to find deleted users
        $user = User::withTrashed()->findOrFail($userId);
        $name = $user->name;

        // Force delete the user, which should cascade to related models (Costumes, Orders, Store)
        $user->forceDelete();

        return Redirect::route('admin.soft_delete.index')->with('status', 'User ' . $name . ' and all associated data have been PERMANENTLY ERASED.');
    }

    /**
     * Soft deletes a user (moves to trash).
     */
    public function softDeleteUser(int $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);

        if (Auth::id() === $userId) {
            return Redirect::back()->withErrors(['error' => 'You cannot soft delete yourself while logged in.']);
        }

        $user->delete(); // Soft Delete

        return Redirect::route('admin.users')->with('status', 'User ' . $user->name . ' moved to Trash bin. Review there to restore or permanent delete.');
    }

    /**
     * Restore a soft-deleted banner.
     */
    public function restoreBanner(int $bannerId): RedirectResponse
    {
        $banner = \App\Models\CatalogBanner::onlyTrashed()->findOrFail($bannerId);

        // Logic: Put it at the end of the current active list
        $nextOrder = \App\Models\CatalogBanner::max('order') + 1;
        $banner->order = $nextOrder ?: 1;

        $banner->restore();

        return Redirect::route('admin.soft_delete.index')->with('status', "Banner '{$banner->title}' restored to position {$banner->order}.");
    }

    /**
     * Permanently delete a banner and its image file.
     */
    public function forceDeleteBanner(int $bannerId): RedirectResponse
    {
        $banner = \App\Models\CatalogBanner::onlyTrashed()->findOrFail($bannerId);

        // NOW we delete the actual file because it's being erased from the galaxy!
        if ($banner->image_path && \Storage::disk('public')->exists($banner->image_path)) {
            \Storage::disk('public')->delete($banner->image_path);
        }

        $banner->forceDelete();

        return Redirect::route('admin.soft_delete.index')->with('status', "Banner permanently erased from storage.");
    }

    /**
     * Show a list of all costumes with their current discount status.
     */
    public function manageDiscounts(): View
    {
        // Fetch all costumes with their renter store information
        $costumes = Costume::with('renter.store')->get()->map(function ($costume) {
            // Calculate dynamic properties for display
            $costume->original_price = $costume->price_per_day;
            $costume->final_price = $costume->final_price; // Uses accessor
            $costume->is_on_sale = $costume->is_on_sale; // Uses accessor
            return $costume;
        });

        return view('admin.manage_discounts', compact('costumes'));
    }

    /**
     * Show list of all Banners for modular management.
     */
    public function manageBanners(): View
    {
        $banners = CatalogBanner::orderBy('order')->get();
        $maxBanners = 5;
        $minBanners = 1;

        // 💥 Pass these limits to the view
        return view('admin.manage_banners', compact('banners', 'maxBanners', 'minBanners'));
    }

    /**
     * Handle adding a new banner, enforcing the max count limit (5).
     */
    public function addBanner(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'image_file' => 'required|image|mimes:jpeg,png,jpg|max:3048',
        ]);

        $currentCount = CatalogBanner::count();
        $maxBanners = 5;

        if ($currentCount >= $maxBanners) {
            return Redirect::back()->withErrors(['banner_limit' => 'Maximum limit of 5 banners reached.']);
        }

        $imagePath = $request->file('image_file')->store('banners', 'public');
        $nextOrder = CatalogBanner::max('order') + 1;
        if ($nextOrder === null) {
            $nextOrder = 1;
        }

        CatalogBanner::create([
            'title' => $validated['title'],
            'image_path' => $imagePath,
            'order' => $nextOrder,
        ]);

        return Redirect::route('admin.banners.manage')->with('status', 'New banner added successfully!');
    }

    /**
     * Handle deleting a banner, enforcing the minimum count limit (1).
     */
    public function deleteBanner(int $bannerId): RedirectResponse
    {
        $banner = CatalogBanner::findOrFail($bannerId);
        $currentCount = CatalogBanner::count();
        $minBanners = 1;

        if ($currentCount <= $minBanners) {
            return Redirect::back()->withErrors(['banner_limit' => 'Cannot delete the last banner. Minimum limit of 1 banner required.']);
        }

        $oldOrder = $banner->order;
        $bannerName = $banner->title;

        // 💥 REMOVE OR COMMENT OUT THESE LINES:
        /*
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }
        */

        // This now only marks 'deleted_at' in the DB because of the SoftDeletes trait!
        $banner->delete();

        // Reorder remaining banners so the numbers stay 1, 2, 3...
        CatalogBanner::where('order', '>', $oldOrder)->decrement('order');

        return Redirect::route('admin.banners.manage')->with('status', "Banner '{$bannerName}' moved to trash.");
    }

    /**
     * Swaps the order of two adjacent banners.
     */
    public function swapBannerOrder(Request $request, int $bannerId): RedirectResponse
    {
        $banner = CatalogBanner::findOrFail($bannerId);
        $direction = $request->input('direction');

        $neighbor = CatalogBanner::where('order', $direction === 'up' ? $banner->order - 1 : $banner->order + 1)->first();

        if ($neighbor) {
            // Swap order numbers using a temporary high ID to avoid UNIQUE constraint violation
            $oldOrder = $banner->order;
            $newOrder = $neighbor->order;

            try {
                DB::transaction(function () use ($banner, $neighbor, $oldOrder, $newOrder) {
                    // 1. Temporarily set one banner's order to a safe, non-conflicting number (e.g., max order + 1)
                    $banner->order = CatalogBanner::max('order') + 1;
                    $banner->save();

                    // 2. Set the neighbor's order to the main banner's old order
                    $neighbor->order = $oldOrder;
                    $neighbor->save();

                    // 3. Set the main banner's order to the neighbor's old order
                    $banner->order = $newOrder;
                    $banner->save();
                });

                return Redirect::back()->with('status', 'Banner order swapped successfully!');
            } catch (\Exception $e) {
                // Log the error and return a generic fail message if the transaction fails unexpectedly
                \Log::error("Banner swap failed: " . $e->getMessage());
                return Redirect::back()->withErrors(['order_error' => 'An internal database error occurred during the swap.']);
            }
        }

        return Redirect::back()->withErrors(['order_error' => 'Cannot move banner further in that direction.']);
    }
}
