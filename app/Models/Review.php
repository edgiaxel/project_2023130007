<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'costume_id', 'order_id', 'rating', 'comment', 'images'];
    protected $casts = ['images' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function costume()
    {
        return $this->belongsTo(Costume::class);
    }
}
