<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\ReviewModerationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(int $orderId)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->doesntHave('review') // 💥 PREVENT DUPLICATES
            ->findOrFail($orderId);

        return view('user.leave_review', compact('order'));
    }

    public function store(Request $request, int $orderId)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->doesntHave('review')
            ->findOrFail($orderId);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'costume_id' => $order->costume_id,
            'order_id' => $order->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? '',
        ]);

        return redirect()->route('user.orders')->with('status', 'Review transmitted! Social credit improved! ✨');
    }

    public function requestModeration(Request $request, int $reviewId)
    {
        $review = Review::with('costume')->findOrFail($reviewId);
        if ($review->costume->user_id !== Auth::id())
            abort(403);

        $request->validate(['reason' => 'required|string|max:500']);

        ReviewModerationRequest::create([
            'review_id' => $reviewId,
            'renter_id' => Auth::id(),
            'reason' => $request->reason,
            'status' => 'pending'
        ]);

        return back()->with('status', 'Council alert sent. Admin will review the review.');
    }

    
}