<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Costume;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CostumeController extends Controller
{
    /**
     * Display a listing of the costumes owned by the current renter.
     */
    public function index()
    {
        $costumes = Auth::user()->costumes()->withCount('orders')->get();

        return view('renter.manage_costumes', compact('costumes'));
    }

    /**
     * Handle the incoming request to store a newly uploaded costume.
     */
    public function store(Request $request): RedirectResponse
    {
        // ... (Store logic unchanged, relies on 'costume:create' permission)
        $validated = $request->validate([
            'character_name' => ['required', 'string', 'max:255'],
            'media_series' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:50'],
            'condition' => ['required', 'in:new,excellent,good,worn'],
            'price' => ['required', 'integer', 'min:1000'],
            'stock' => ['required', 'integer', 'min:1', 'max:99'],
            'tags' => ['required', 'string'],
            'images' => ['required', 'array', 'max:1'], // Only allowing one image for the 'main_image_path' for now
            'images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:4096'],
        ]);

        $mainImagePath = null;
        if ($request->hasFile('images')) {
            $mainImagePath = $request->file('images')[0]->store('costumes', 'public');
        }

        Costume::create([
            'user_id' => Auth::id(),
            'name' => $validated['character_name'],
            'series' => $validated['media_series'],
            'size' => $validated['size'],
            'condition' => $validated['condition'],
            'price_per_day' => $validated['price'],
            'stock' => $validated['stock'],
            'tags' => explode(',', $validated['tags']),
            'main_image_path' => $mainImagePath,
            'is_approved' => false, // Requires Admin Approval!
        ]);

        return Redirect::route('renter.costumes.manage')->with('status', 'Costume submitted! Awaiting Admin approval.');
    }

    /**
     * Show the form for editing the specified costume.
     * FIX: Allows Admin/Owner to access without being the costume owner.
     */
    public function edit(int $costume_id): View
    {
        $costume = Costume::findOrFail($costume_id);
        $currentUser = Auth::user();

        // Check 1: Is the current user the owner?
        $isOwner = $costume->user_id === $currentUser->id;

        // Check 2: Does the current user have 'edit-all' permission (Admin/Owner)?
        $canEditAll = $currentUser->hasPermissionTo('costume:edit-all');

        if (!$isOwner && !$canEditAll) {
            abort(403, 'ACCESS DENIED: You are not authorized to edit this costume.');
        }

        return view('renter.edit_costume', compact('costume'));
    }

    /**
     * Update the specified costume in storage.
     * FIX: Allows Admin/Owner to update without being the costume owner.
     */
    public function update(Request $request, int $costume_id): RedirectResponse
    {
        $costume = Costume::findOrFail($costume_id);
        $currentUser = Auth::user();

        // Authorization Check
        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:edit-all')) {
            abort(403, 'ACCESS DENIED: You cannot update a costume you do not own.');
        }

        $validated = $request->validate([
            'character_name' => ['required', 'string', 'max:255'],
            'media_series' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:50'],
            'condition' => ['required', 'in:new,excellent,good,worn'],
            'price' => ['required', 'integer', 'min:1000'],
            'stock' => ['required', 'integer', 'min:1', 'max:99'],
            'tags' => ['required', 'string'],
            'main_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:4096'],
        ]);

        // 1. Handle Image Replacement
        $mainImagePath = $costume->main_image_path;
        if ($request->hasFile('main_image')) {
            if ($mainImagePath && Storage::disk('public')->exists($mainImagePath)) {
                Storage::disk('public')->delete($mainImagePath);
            }
            $mainImagePath = $request->file('main_image')->store('costumes', 'public');
        }

        // 2. Update Costume
        $costume->update([
            'name' => $validated['character_name'],
            'series' => $validated['media_series'],
            'size' => $validated['size'],
            'condition' => $validated['condition'],
            'price_per_day' => $validated['price'],
            'stock' => $validated['stock'],
            'tags' => explode(',', $validated['tags']),
            'main_image_path' => $mainImagePath,
            // Maintain approval status on edit if it was already approved
            'is_approved' => $costume->is_approved,
        ]);

        // 💥 FIX REDIRECT LOGIC: Send Admin/Owner back to the Admin view of the store.
        if ($currentUser->hasPermissionTo('costume:edit-all')) {
            // Redirect Admin/Owner to the store view they came from.
            return Redirect::route('admin.stores.view', $costume->user_id)->with('status', $costume->name . ' updated successfully (Admin Override).');
        }

        // Default: Redirect Renter to their own management page.
        return Redirect::route('renter.costumes.manage')->with('status', $costume->name . ' updated successfully!');
    }

    /**
     * Soft Delete the specified costume.
     * FIX: Allows Admin/Owner to delete without being the costume owner.
     */
    public function destroy(int $costume_id): RedirectResponse
    {
        $costume = Costume::findOrFail($costume_id);
        $currentUser = Auth::user();

        // Authorization Check
        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:delete-all')) {
            abort(403, 'ACCESS DENIED: You cannot delete a costume you do not own.');
        }

        // Soft Delete (uses SoftDeletes trait)
        $costume->delete();

        // 💥 FIX REDIRECT LOGIC: Send Admin/Owner back to the Admin view of the store.
        if ($currentUser->hasPermissionTo('costume:delete-all')) {
            // Redirect Admin/Owner to the store view they came from.
            return Redirect::route('admin.stores.view', $costume->user_id)->with('status', $costume->name . ' moved to trash (soft deleted) by Admin.');
        }

        // Default: Redirect Renter to their own management page.
        return Redirect::route('renter.costumes.manage')->with('status', $costume->name . ' moved to trash (soft deleted).');
    }
}