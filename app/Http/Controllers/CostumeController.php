<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Costume;
use App\Models\CostumeImage;
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
        // 1. Validation (MAX file size is 4096 KB (4MB) per file, MIN is 1 file, MAX is 5 files total)
        $validated = $request->validate([
            'character_name' => ['required', 'string', 'max:255'],
            'media_series' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:50'],
            'condition' => ['required', 'in:new,excellent,good,worn'],
            'price' => ['required', 'integer', 'min:1000'],
            'stock' => ['required', 'integer', 'min:1', 'max:99'],
            'tags' => ['required', 'string'],
            // 💥 NEW: Discount Validation
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'in:percentage,fixed'],
            'is_discount_active' => ['nullable', 'boolean'],

            // 💥 RELAXED: Only require array and check file type/mimes, trusting PHP.ini for large size
            'images' => ['required', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg'],
        ]);

        // 2. Create the Costume record first (MUST happen before saving images)
        $costume = Costume::create([
            'user_id' => Auth::id(),
            'name' => $validated['character_name'],
            'series' => $validated['media_series'],
            'size' => $validated['size'],
            'condition' => $validated['condition'],
            'price_per_day' => $validated['price'],
            'stock' => $validated['stock'],
            'tags' => explode(',', $validated['tags']),
            'status' => 'pending',
            // 💥 NEW: Discount Fields
            'discount_value' => $validated['discount_value'] ?? null,
            'discount_type' => $validated['discount_type'] ?? null,
            'is_discount_active' => $request->has('is_discount_active'), // Checkbox handling
        ]);

        // 3. Handle Image Uploads and record saving
        if ($request->hasFile('images')) {
            $imagesToSave = [];
            $order = 0;
            foreach ($request->file('images') as $image) {
                $path = $image->store('costumes', 'public');
                $imagesToSave[] = [
                    'costume_id' => $costume->id,
                    'image_path' => $path,
                    'order' => $order++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // Mass insert images to the related table
            CostumeImage::insert($imagesToSave);
        }

        return Redirect::route('renter.costumes.manage')->with('status', 'Costume submitted! Awaiting Admin approval.');
    }

    /**
     * Show the form for editing the specified costume.
     * FIX: Allows Admin/Owner to access without being the costume owner.
     */
    public function edit(int $costume_id): View
    {
        // 💥 FIX: Eager load the 'images' relationship here!
        $costume = Costume::with('images')->findOrFail($costume_id);
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
     * Update the specified costume in storage (TEXT FIELDS ONLY).
     */
    public function update(Request $request, int $costume_id): RedirectResponse
    {
        $costume = Costume::with('images')->findOrFail($costume_id);
        $currentUser = Auth::user();

        // Authorization Check (unchanged)
        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:edit-all')) {
            abort(403, 'ACCESS DENIED: You cannot update a costume you do not own.');
        }

        // 1. Validation (TEXT FIELDS ONLY)
        $validated = $request->validate([
            'character_name' => ['required', 'string', 'max:255'],
            'media_series' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:50'],
            'condition' => ['required', 'in:new,excellent,good,worn'],
            'price' => ['required', 'integer', 'min:1000'],
            'stock' => ['required', 'integer', 'min:1', 'max:99'],
            'tags' => ['required', 'string'],
            // 💥 NEW: Discount Validation (TEXT form only handles these)
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'in:percentage,fixed'],
            'is_discount_active' => ['required', 'in:1,0'], // Expecting "1" or "0"
        ]);

        // 2. Update Costume (Text fields)
        $costume->update([
            'name' => $validated['character_name'],
            'series' => $validated['media_series'],
            'size' => $validated['size'],
            'condition' => $validated['condition'],
            'price_per_day' => $validated['price'],
            'stock' => $validated['stock'],
            'tags' => explode(',', $validated['tags']),
            'status' => $costume->status,

            // 💥 NEW: Discount Fields
            'discount_value' => $validated['discount_value'] ?? null,
            'discount_type' => $validated['discount_type'] ?? null,
            // 💥 FIX: Explicitly convert the string "1"/"0" to a boolean
            'is_discount_active' => $request->is_discount_active == '1',
        ]);

        $message = $costume->name . ' details updated successfully!';

        // 💥 NEW REDIRECT LOGIC
        if ($request->input('redirect_to') === 'discounts' && $currentUser->hasRole('admin')) {
            return Redirect::route('admin.discounts.manage')->with('status', $message);
        }// Default: Your existing logic
        $redirectRoute = $currentUser->hasPermissionTo('costume:edit-all') ?
            route('admin.stores.view', $costume->user_id) :
            route('renter.costumes.manage');

        return Redirect::to($redirectRoute)->with('status', $message);
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

    /**
     * Restore a soft-deleted costume.
     * Accessible by ADMIN/OWNER only.
     */
    public function restore(int $costume_id): RedirectResponse
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser->hasPermissionTo('costume:delete-all')) {
            abort(403, 'ACCESS DENIED: You cannot restore costumes.');
        }

        // Use withTrashed() to find deleted costumes
        $costume = Costume::withTrashed()->findOrFail($costume_id);
        $costume->restore();

        return Redirect::route('admin.soft_delete.index')->with('status', $costume->name . ' has been restored.');
    }

    /**
     * Permanently delete a soft-deleted costume.
     * Accessible by ADMIN/OWNER only.
     */
    public function forceDelete(int $costume_id): RedirectResponse
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (!$currentUser->hasPermissionTo('costume:delete-all')) {
            abort(403, 'ACCESS DENIED: You cannot permanently delete costumes.');
        }

        // Use withTrashed() to find deleted costumes
        $costume = Costume::withTrashed()->findOrFail($costume_id);
        $name = $costume->name;
        $costume->forceDelete();

        return Redirect::route('admin.soft_delete.index')->with('status', $name . ' has been permanently erased.');
    }/**
     * Deletes a specific image from a costume listing.
     * Accessible by Renter (if owned) or Admin/Owner.
     */
    public function deleteImage(int $imageId): RedirectResponse
    {
        $image = CostumeImage::with('costume')->findOrFail($imageId);
        $costume = $image->costume;
        $currentUser = Auth::user();

        // Authorization Check
        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:edit-all')) {
            abort(403, 'ACCESS DENIED: You cannot delete images for this costume.');
        }

        // Check if this is the last image
        if ($costume->images()->count() <= 1) {
            return Redirect::back()->withErrors(['image_error' => 'Cannot delete the last image. A costume must have at least one image.']);
        }

        $imagePath = $image->image_path;

        // Delete file from storage
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        // Delete the database record
        $image->delete();

        // Recalculate order of remaining images
        $costume->images()->get()->each(function ($img, $index) {
            $img->order = $index;
            $img->save();
        });

        // 💥 FIX: Always redirect to the current costume edit page (renter.costumes.edit) 
        // to stay on the page regardless of role access path.
        $redirectRoute = route('renter.costumes.edit', $costume->id);

        return Redirect::to($redirectRoute)->with('status', 'Image deleted successfully.');
    }

    /**
     * Reorders the images for a costume.
     */
    public function reorderImages(Request $request, int $costumeId): RedirectResponse
    {
        $costume = Costume::with('images')->findOrFail($costumeId);
        $currentUser = Auth::user();

        // Authorization Check
        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:edit-all')) {
            abort(403, 'ACCESS DENIED: You cannot reorder images for this costume.');
        }

        $validated = $request->validate([
            // Array of image IDs in their new ordered sequence
            'image_order' => ['required', 'array'],
            'image_order.*' => ['exists:costume_images,id'],
        ]);

        $order = 0;
        foreach ($validated['image_order'] as $imageId) {
            $costume->images()->where('id', $imageId)->update(['order' => $order++]);
        }

        return Redirect::back()->with('status', 'Image order updated successfully!');
    }

    /**
     * Helper action to quickly set an image as the main (order 0) image.
     */
    public function setMainImage(int $costumeId, int $imageId): RedirectResponse
    {
        $costume = Costume::with('images')->findOrFail($costumeId);
        $currentUser = Auth::user();

        // Authorization Check
        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:edit-all')) {
            abort(403, 'ACCESS DENIED: You cannot set the main image for this costume.');
        }

        $mainImage = $costume->images()->where('id', $imageId)->firstOrFail();

        // 1. Swap the current 'main' image (order 0) to a new order (e.g., 99 temporarily)
        $costume->images()->where('order', 0)->update(['order' => 99]);

        // 2. Set the requested image to order 0
        $mainImage->update(['order' => 0]);

        // 3. Recalculate order of everything else sequentially, skipping the new main image
        $costume->images()->where('id', '!=', $imageId)->orderBy('order')->get()->each(function ($img, $index) {
            $img->order = $index + 1; // Start counting from 1
            $img->save();
        });

        return Redirect::back()->with('status', $mainImage->id . ' has been set as the main image.');
    }

    /**
     * Swaps the order of an image with its neighbor (left or right).
     */
    public function swapImageOrder(Request $request, int $costumeId): RedirectResponse
    {
        $costume = Costume::with('images')->findOrFail($costumeId);
        $currentUser = Auth::user();

        // Authorization Check
        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:edit-all')) {
            abort(403, 'ACCESS DENIED: Not authorized to swap image order.');
        }

        $validated = $request->validate([
            'image_id' => 'required|exists:costume_images,id',
            'direction' => 'required|in:left,right',
        ]);

        $currentImage = $costume->images->firstWhere('id', $validated['image_id']);
        if (!$currentImage) {
            return Redirect::back()->withErrors(['error' => 'Image not found.']);
        }

        $currentOrder = $currentImage->order;
        $targetOrder = $validated['direction'] === 'left' ? max(0, $currentOrder - 1) : $currentOrder + 1;

        // Find the image currently occupying the target slot
        $neighborImage = $costume->images->firstWhere('order', $targetOrder);

        if ($neighborImage) {
            // Swap orders
            $currentImage->order = $targetOrder;
            $neighborImage->order = $currentOrder;
            $currentImage->save();
            $neighborImage->save();
        } else if ($validated['direction'] === 'left' && $currentOrder > 0) {
            // If moving left to position 0 (and no neighbor was found at -1), just set order to 0
            $currentImage->order = 0;
            $currentImage->save();
        } else if ($validated['direction'] === 'right') {
            // If moving right and no neighbor exists (at the end), do nothing or set to max+1 order, but let's stick to do nothing.
            return Redirect::back()->withErrors(['error' => 'Cannot move image further right.']);
        }

        return Redirect::back()->with('status', 'Image order swapped successfully!');
    }

    /**
     * Handles replacement and addition of images for an existing costume.
     */
    public function updateImages(Request $request, int $costume_id): RedirectResponse
    {
        $costume = Costume::with('images')->findOrFail($costume_id);
        $currentUser = Auth::user();

        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:edit-all')) {
            abort(403, 'ACCESS DENIED: You cannot update images for this costume.');
        }

        // 1. Validation (Image specific fields only)
        $validated = $request->validate([
            'image_replace_*' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:6144'],
            'new_images' => ['nullable', 'array'],
            'new_images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:6144'],
        ]);

        $imagesUpdated = false;

        // 2. Handle Image Replacement/Update (based on dynamic fields in the request)
        foreach ($costume->images as $image) {
            $fieldName = 'image_replace_' . $image->id;
            if ($request->hasFile($fieldName)) {
                $newFile = $request->file($fieldName);
                $newImagePath = $newFile->store('costumes', 'public');

                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->update(['image_path' => $newImagePath]);
                $imagesUpdated = true;
            }
        }

        // 3. Handle brand NEW image uploads
        if ($request->hasFile('new_images')) {
            $newFiles = $request->file('new_images');
            $newFiles = collect($newFiles)->filter(); // Filter out empty strings/nulls

            if ($newFiles->isNotEmpty()) {
                $currentCount = $costume->images->count();

                if ($currentCount + $newFiles->count() > 5) {
                    return Redirect::back()->withErrors(['image_error' => 'You can only have a maximum of 5 images per costume.']);
                }

                $imagesToSave = [];
                $order = $costume->images->max('order') + 1;
                if ($order === null)
                    $order = 0;

                foreach ($newFiles as $image) {
                    $path = $image->store('costumes', 'public');
                    $imagesToSave[] = [
                        'costume_id' => $costume->id,
                        'image_path' => $path,
                        'order' => $order++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                CostumeImage::insert($imagesToSave);
                $imagesUpdated = true;
            }
        }

        $message = $imagesUpdated ? 'Images updated/added successfully!' : 'No new images were processed.';

        // Redirect logic remains, but we redirect to the current edit page
        $redirectRoute = $currentUser->hasPermissionTo('costume:edit-all') ?
            route('admin.stores.view', $costume->user_id) :
            route('renter.costumes.edit', $costume->id);

        return Redirect::to($redirectRoute)->with('status', $message);
    }

    /**
     * Handles bulk addition of new images to a costume.
     */
    public function addImage(Request $request, int $costume_id): RedirectResponse
    {
        $costume = Costume::with('images')->findOrFail($costume_id);
        $currentUser = Auth::user();

        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:edit-all')) {
            abort(403, 'ACCESS DENIED.');
        }

        $validated = $request->validate([
            'new_images' => ['required', 'array', 'min:1'],
            'new_images.*' => ['image', 'mimes:jpeg,png,jpg', 'max:6144'], // Max 6MB per file
        ]);

        $newFiles = collect($request->file('new_images'))->filter();
        $currentCount = $costume->images->count();

        if ($currentCount + $newFiles->count() > 5) {
            return Redirect::back()->withErrors(['image_error' => 'Maximum limit of 5 images exceeded.']);
        }

        $imagesToSave = [];
        $order = $costume->images->max('order') + 1;
        if ($order === null)
            $order = 0;

        foreach ($newFiles as $image) {
            $path = $image->store('costumes', 'public');
            $imagesToSave[] = [
                'costume_id' => $costume->id,
                'image_path' => $path,
                'order' => $order++,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        CostumeImage::insert($imagesToSave);
        return Redirect::back()->with('status', 'New images uploaded successfully.');
    }

    /**
     * Replaces a specific existing image file.
     */
    public function replaceImage(Request $request, int $image_id): RedirectResponse
    {
        $image = CostumeImage::with('costume')->findOrFail($image_id);
        $costume = $image->costume;
        $currentUser = Auth::user();

        if ($costume->user_id !== $currentUser->id && !$currentUser->hasPermissionTo('costume:edit-all')) {
            abort(403, 'ACCESS DENIED.');
        }

        $validated = $request->validate([
            'replacement_file' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:6144'],
        ]);

        $newFile = $validated['replacement_file'];
        $newImagePath = $newFile->store('costumes', 'public');

        // Delete old file
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Update database record path
        $image->update(['image_path' => $newImagePath]);

        return Redirect::back()->with('status', 'Image replacement successful.');
    }
}