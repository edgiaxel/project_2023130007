<?php

// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes; 

    protected $casts = [
        'start_date' => 'date', 
        'end_date' => 'date', 
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

    public function costume()
    {
        return $this->belongsTo(Costume::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); 
    }
}