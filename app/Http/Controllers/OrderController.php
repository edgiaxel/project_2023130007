<?php

namespace App\Http\Controllers;

use App\Models\Costume;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;   

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

    /**
     * Confirms a pending order and updates stock availability status.
     * Accessible by RENTER and ADMIN.
     */
    public function confirm(Request $request, int $order_id): RedirectResponse
    {
        $order = Order::with('costume')->findOrFail($order_id);

        // Security Check: Only the associated Renter or Admin can confirm
        if (Auth::id() !== $order->costume->user_id && !Auth::user()->hasRole('admin')) {
            return Redirect::back()->withErrors(['auth_error' => 'You do not have cosmic clearance to confirm this order.']);
        }

        if ($order->status === 'waiting') {
            $order->status = 'confirmed';
            $order->save();
            return Redirect::route('renter.orders')->with('status', 'Order confirmed! Waiting for pickup/shipment.');
        }

        return Redirect::back()->withErrors(['status_error' => 'Order cannot be confirmed from its current status.']);
    }

    /**
     * Rejects a pending order and increments the stock back up.
     * Accessible by RENTER and ADMIN.
     */
    public function reject(Request $request, int $order_id): RedirectResponse
    {
        $order = Order::with('costume')->findOrFail($order_id);

        if (Auth::id() !== $order->costume->user_id && !Auth::user()->hasRole('admin')) {
            return Redirect::back()->withErrors(['auth_error' => 'You do not have cosmic clearance to reject this order.']);
        }

        if ($order->status === 'waiting') {
            $order->status = 'rejected';
            $order->save();

            // Stock Management: Return stock when order is rejected
            $order->costume->increment('stock');

            return Redirect::route('renter.orders')->with('status', 'Order rejected! Stock restored.');
        }

        return Redirect::back()->withErrors(['status_error' => 'Only waiting orders can be rejected.']);
    }

    /**
     * Generic status update for ongoing orders (e.g., Mark as Borrowed or Returned).
     * Accessible by RENTER and ADMIN.
     */
    public function updateStatus(Request $request, int $order_id): RedirectResponse
    {
        $order = Order::with('costume')->findOrFail($order_id);

        $newStatus = $request->input('new_status');
        $allowedStatuses = ['borrowed', 'returned', 'completed']; // Allowed status transitions

        if (Auth::id() !== $order->costume->user_id && !Auth::user()->hasRole('admin')) {
            return Redirect::back()->withErrors(['auth_error' => 'You lack the authorization to update this status.']);
        }

        if (!in_array($newStatus, $allowedStatuses)) {
            return Redirect::back()->withErrors(['status_error' => 'Invalid status provided.']);
        }

        // Handle Stock Return (Completed/Returned status)
        if (in_array($newStatus, ['returned', 'completed']) && $order->status !== 'returned' && $order->status !== 'completed') {
            // Only return stock once, after it has been borrowed/confirmed
            $order->costume->increment('stock');
            $message = 'Order marked as returned and stock restored.';
        } else {
            $message = 'Order status updated successfully.';
        }

        $order->status = $newStatus;
        $order->save();

        return Redirect::route('renter.orders')->with('status', $message);
    }

    /**
     * Show the detailed view of an order (Admin, Renter, or Customer view).
     */
    public function viewDetail(int $order_id): View
    {
        // Load order and necessary relationships
        $order = Order::with(['costume.renter.store', 'user'])->findOrFail($order_id);
        
        // Authorization Check: Must be Admin, or the Customer who placed it, or the Renter who owns the costume.
        if (Auth::user()->hasRole('admin') || Auth::id() === $order->user_id || Auth::id() === $order->costume->user_id) {
            return view('order.detail', compact('order'));
        }

        // SCREAM at unauthorized access
        abort(403, 'Unauthorized access to order details!');
    }
}