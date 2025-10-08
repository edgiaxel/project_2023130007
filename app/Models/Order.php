<?php

// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- ADD THIS

class Order extends Model
{
    use HasFactory, SoftDeletes; // <-- USE SOFTDELETES

    protected $casts = [
        'start_date' => 'date', // Casts to Carbon object
        'end_date' => 'date',   // Casts to Carbon object
    ];
    protected $fillable = [
        'order_code',
        'costume_id',
        'user_id',
        'start_date',
        'end_date',
        'total_price',
        'status'
    ];

    // Relationships
    public function costume()
    {
        return $this->belongsTo(Costume::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // The customer
    }
}