<?php

namespace App\Http\Controllers;

use App\Models\Costume;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon; 
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function createOrder(int $costumeId)
    {
        $costume = Costume::findOrFail($costumeId);
        return view('user.place_order', [
            'costume' => $costume,
        ]);
    }

    public function storeOrder(Request $request): RedirectResponse
    {
        // 1. Validation.
        $request->validate([
            'costume_id' => 'required|exists:costumes,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // 2. Fetch the Costume
        $costume = Costume::findOrFail($request->costume_id);

        if ($costume->stock < 1) {
            return Redirect::back()->withErrors(['stock_error' => 'This costume is currently out of stock. Maybe pick something else?']);
        }

        $existingActiveOrder = Order::where('user_id', Auth::id())
            ->where('costume_id', $costume->id)
            ->whereIn('status', ['waiting', 'confirmed', 'borrowed'])
            ->exists();

        if ($existingActiveOrder) {
            return Redirect::back()->withErrors(['stock_error' => 'You already have an active rental or pending order for this specific costume.']);
        }

        // 3. Calculate the dynamic price
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $durationInDays = $startDate->diffInDays($endDate) + 1;
        $totalPrice = $costume->price_per_day * $durationInDays;

        // 4. Create a unique Order Code 
        $orderCode = 'ORD-' . now()->year . now()->format('md') . Auth::id() . rand(100, 999);

        // 5. Create the Order
        $order = Order::create([
            'order_code' => $orderCode,
            'costume_id' => $costume->id,
            'user_id' => Auth::id(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $totalPrice,
            'status' => 'waiting',
        ]);

        // 6. DECREMENT the stock count
        $costume->decrement('stock');

        // 7. Redirect the user
        return Redirect::route('user.orders')->with('status', 'Order placed successfully! Renter is being notified.');
    }
}