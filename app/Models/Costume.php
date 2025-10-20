<?php

// app/Models/Costume.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Costume extends Model
{
    use HasFactory, SoftDeletes; 

    protected $fillable = [
        'user_id',
        'name',
        'series',
        'size',
        'condition',
        'price_per_day',
        'stock',
        'main_image_path',
        'is_approved',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_approved' => 'boolean',
    ];

    public function renter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}