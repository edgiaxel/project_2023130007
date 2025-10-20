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
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

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
        $users = User::whereHas('roles', fn($q) => $q->whereIn('name', ['renter', 'user']))->with('roles', 'store')->get();
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
        // Ideally fetch costume-specific discounts here too
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
     * Show the profile edit form for a specific user (accessible only by Admin).
     */
    public function editUser(int $userId): View
    {
        $user = User::with('store')->findOrFail($userId);
        $isAdminView = true;

        return view('admin.edit_user_profile', compact('user', 'isAdminView'));
    }

    /**
     * Handle updating another user's profile information (accessible only by Admin).
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
}