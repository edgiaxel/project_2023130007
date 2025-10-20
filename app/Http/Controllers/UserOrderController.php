<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserOrderController extends Controller
{
    /**
     * Display the user's order tracking page with all their orders.
     */
    public function index(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['costume.renter', 'user'])
            ->latest()
            ->get();
        
        return view('user.track_orders', compact('orders'));
    }
}