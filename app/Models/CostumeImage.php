<?php
// FILE: app/Models/CostumeImage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostumeImage extends Model
{
    use HasFactory;

    protected $fillable = ['costume_id', 'image_path', 'order'];

    public $table = 'costume_images';

    public function costume()
    {
        return $this->belongsTo(Costume::class);
    }
}