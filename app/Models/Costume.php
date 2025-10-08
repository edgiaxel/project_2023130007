<?php

// app/Models/Costume.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- ADD THIS

class Costume extends Model
{
    use HasFactory, SoftDeletes; // <-- USE SOFTDELETES

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

    // Cast the 'tags' column to an array so Laravel handles JSON encoding/decoding
    protected $casts = [
        'tags' => 'array',
        'is_approved' => 'boolean',
    ];

    // Relationship to the Renter
    public function renter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}