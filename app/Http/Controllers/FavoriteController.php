<?php

namespace App\Http\Controllers;

use App\Models\Costume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favorites()->with(['renter.store', 'images'])->get();
        return view('user.favorites', compact('favorites'));
    }

    public function toggle(int $costumeId)
    {
        $user = Auth::user();
        $user->favorites()->toggle($costumeId);

        // 💥 ALWAYS REDIRECT AWAY FROM THE POST ROUTE
        if (url()->previous() === route('user.favorites')) {
            return redirect()->route('user.favorites')->with('status', 'Wishlist updated!');
        }

        // Default to going back to the Costume Detail page
        return back()->with('status', 'Cosmic wishlist updated!');
    }
}