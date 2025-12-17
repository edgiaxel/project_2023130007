<?php

// app/Models/Costume.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Costume extends Model
{
    use HasFactory, SoftDeletes;
    // 💥 MODIFIED: Removed 'main_image_path'
    protected $fillable = ['user_id', 'name', 'series', 'size', 'condition', 'price_per_day', 'stock', 'status', 'tags', 'original_status', 'discount_value', 'discount_type', 'discount_start_date', 'discount_end_date', 'is_discount_active'];

    protected $casts = ['tags' => 'array',];
    public function renter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // 💥 NEW: One-to-Many relationship for images
    public function images()
    {
        return $this->hasMany(CostumeImage::class)->orderBy('order');
    }

    /**
     * Calculates the price after applying the current active discount.
     */
    /**
     * Calculates the price after applying the current active discount. (SIMPLIFIED)
     */
    public function getDiscountedPriceAttribute(): int
    {
        if (!$this->is_discount_active || !$this->discount_value) {
            return $this->price_per_day;
        }

        $price = $this->price_per_day;

        if ($this->discount_type === 'percentage') {
            $discountAmount = $price * ($this->discount_value / 100);
            $newPrice = $price - $discountAmount;
        } else {
            $newPrice = $price - $this->discount_value;
        }

        return max(0, round($newPrice));
    }

    /**
     * Accessor to simplify fetching the final price in the catalog/order process.
     */
    public function getFinalPriceAttribute(): int
    {
        return $this->discounted_price; // Use the calculated value
    }

    /**
     * Checks if the discount is currently active and valid based on dates.
     */
    public function getIsOnSaleAttribute(): bool
    {
        return $this->is_discount_active && $this->discount_value > 0;
    }
}