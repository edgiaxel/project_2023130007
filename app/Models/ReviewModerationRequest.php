<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewModerationRequest extends Model
{
    protected $table = 'review_moderation_requests'; // Explicitly set the table name

    protected $fillable = ['review_id', 'renter_id', 'reason', 'proof_image', 'status'];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }
    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }
}