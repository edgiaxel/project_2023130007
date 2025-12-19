<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Costume;
use App\Models\Order;
use App\Models\Review;
use App\Models\ReviewModerationRequest;
use Illuminate\Database\Seeder;

class SocialFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::role('user')->get();
        $renters = User::role('renter')->get();
        $costumes = Costume::where('status', 'approved')->get();

        // --- 1. SEED WISHLISTS (Favorites) ---
        // Every regular user will like 3-7 random costumes
        foreach ($users as $user) {
            $randomCostumes = $costumes->random(rand(3, 7));
            $user->favorites()->sync($randomCostumes->pluck('id'));
        }

        // Even renters have taste! Renters like 2 random costumes each
        foreach ($renters as $renter) {
            $randomCostumes = $costumes->random(rand(1, 2));
            $renter->favorites()->syncWithoutDetaching($randomCostumes->pluck('id'));
        }

        // --- 2. SEED REVIEWS ---
        // We look for 'completed' orders and leave feedback
        $completedOrders = Order::where('status', 'completed')->get();

        $comments = [
            5 => ['Perfect fit!', 'The fabric is high quality, love it!', 'Best cosplay ever!', 'Arrived clean and smelled like space stardust.'],
            4 => ['Very good, but the shoes were a bit tight.', 'Great quality, but missing one small button.', 'Solid rental experience.'],
            3 => ['It was okay, but looked a bit worn out.', 'Average quality for the price.', 'Size was a bit off.'],
            2 => ['It did not look like the photos.', 'Missing pieces of the armor.', 'Smelled a bit weird.'],
            1 => ['DO NOT RENT. Ripped upon arrival.', 'Total scam, looks like a cheap bedsheet.', 'The renter was rude.']
        ];

        foreach ($completedOrders as $order) {
            // 80% chance to leave a review
            if (rand(1, 10) <= 8) {
                $rating = rand(3, 5); // Most reviews are good
                if (rand(1, 10) == 1) $rating = rand(1, 2); // 10% chance of a "hater" review

                Review::firstOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id' => $order->user_id,
                        'costume_id' => $order->costume_id,
                        'rating' => $rating,
                        'comment' => $comments[$rating][array_rand($comments[$rating])],
                        'created_at' => $order->updated_at,
                    ]
                );
            }
        }

        // --- 3. SEED MODERATION REQUESTS (Flags) ---
        // Let's find some low-rated reviews and have renters "Flag" them
        $badReviews = Review::where('rating', '<=', 2)->get();

        foreach ($badReviews as $review) {
            // Get the owner of the costume
            $renterId = $review->costume->user_id;

            ReviewModerationRequest::firstOrCreate(
                ['review_id' => $review->id],
                [
                    'renter_id' => $renterId,
                    'reason' => 'This user is lying! The armor was sent in perfect condition. This is a targeted attack on my shop reputation!',
                    'status' => 'pending',
                    'created_at' => now(),
                ]
            );
        }
        
        // Add one "Approved" moderation (to show history)
        $approvedReview = Review::where('rating', 3)->first();
        if ($approvedReview) {
            ReviewModerationRequest::create([
                'review_id' => $approvedReview->id,
                'renter_id' => $approvedReview->costume->user_id,
                'reason' => 'Spam comment with random characters.',
                'status' => 'approved',
                'created_at' => now()->subDays(2),
            ]);
            // The review should be deleted in a real scenario, but seeder just shows the request state
        }
    }
}