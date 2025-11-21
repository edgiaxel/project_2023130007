<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\RenterStore;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        $profilePicPath = $user->profile_picture;
        if ($request->hasFile('profile_picture')) {
            if ($profilePicPath && Storage::disk('public')->exists($profilePicPath)) {
                Storage::disk('public')->delete($profilePicPath);
            }
            $profilePicPath = $request->file('profile_picture')->store('user_profiles', 'public');
        }
        $user->profile_picture = $profilePicPath;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateRenterStore(Request $request): RedirectResponse
    {
        $validatedStore = $request->validate([
            'store_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(RenterStore::class)->ignore(Auth::id(), 'user_id')
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'store_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'], 
        ]);

        $user = $request->user();

        $logoPath = $user->store->store_logo_path ?? null;
        if ($request->hasFile('store_logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('store_logo')->store('store_logos', 'public'); 
        }

        RenterStore::updateOrCreate(
            ['user_id' => $user->id],
            [
                'store_name' => $validatedStore['store_name'],
                'description' => $validatedStore['description'],
                'store_logo_path' => $logoPath,
            ]
        );
        return Redirect::route('profile.edit')->with('status', 'renter-store-updated');
    }

    public function editRenterStore(Request $request): View
    {
        return view('renter.store_profile_setup', [
            'user' => $request->user(),
            'store' => $request->user()->store ?? new RenterStore(),
        ]);
    }
}
