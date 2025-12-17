<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RenterStore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'description',
        'store_logo_path',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}