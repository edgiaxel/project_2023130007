<?php

// app/Http/Controllers/CatalogController.php

namespace App\Http\Controllers;

use App\Models\Costume;
use App\Models\User; // We need the User model for searching stores/users
use Illuminate\Http\Request;
use App\Models\CatalogBanner;
use App\Http\Controllers\OrderController; // Ensure import is present

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        OrderController::checkAndExpireOverdueOrders();

        $searchQuery = $request->query('search');
        $perPage = $request->input('per_page', 10);
        $perPageOptions = [10, 25, 50];

        $MEDIA_CATEGORIES = ['Anime', 'Movie', 'Game', 'TV', 'Other'];

        $costumesQuery = Costume::query();

        // 1. Apply Search Filter
        if ($searchQuery) {
            $costumesQuery->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('series', 'like', '%' . $searchQuery . '%')
                    ->orWhereJsonContains('tags', $searchQuery);
            });
        }

        // 2. Fetch all approved costumes with relationships and apply discount
        // 💥 MODIFIED: Rely on model accessors and remove manual calculation
        // 💥 FIX: Add whereHas('renter') to ensure the owner exists and is not deleted!
        $costumesCollection = $costumesQuery->where('status', 'approved')
            ->whereHas('renter') // This automatically filters out costumes with soft-deleted owners
            ->with(['renter.store', 'images'])
            ->get()
            ->map(function ($costume) {
                $costume->media_group = $costume->tags[0] ?? 'Other';
                return $costume;
            });

        // 3. Manual Pagination
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $costumesCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $costumesPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $costumesCollection->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        // 4. Group the entire collection by the explicit media type
        $groupedCostumes = $costumesCollection->groupBy('media_group');

        // 5. Redirection logic (omitted)
        $banners = CatalogBanner::orderBy('order')->get(); // <-- NEW

        return view('catalog', compact('costumesCollection', 'costumesPaginated', 'perPage', 'perPageOptions', 'MEDIA_CATEGORIES', 'groupedCostumes', 'banners'));
    }
}
