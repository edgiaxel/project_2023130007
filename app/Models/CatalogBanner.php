<?php

// app/Models/CatalogBanner.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 💥 NEW IMPORT

class CatalogBanner extends Model
{
    use HasFactory, SoftDeletes; // 💥 NEW TRAIT

    protected $fillable = ['title', 'image_path', 'order'];
}