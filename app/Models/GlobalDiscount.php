<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalDiscount extends Model
{
    protected $fillable = ['rate', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'rate' => 'float'];
}
