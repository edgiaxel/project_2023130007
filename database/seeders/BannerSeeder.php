<?php

// database/seeders/BannerSeeder.php

namespace Database\Seeders;

use App\Models\CatalogBanner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        CatalogBanner::firstOrCreate(['order' => 1], [
            'title' => 'Anime & Manga Discount!',
            'image_path' => 'banners/1.jpg',
        ]);
        CatalogBanner::firstOrCreate(['order' => 2], [
            'title' => 'Movie & TV Event Savings!',
            'image_path' => 'banners/2.jpg',
        ]);
        CatalogBanner::firstOrCreate(['order' => 3], [
            'title' => 'Game Character Sale!',
            'image_path' => 'banners/3.jpg',
        ]);
    }
}