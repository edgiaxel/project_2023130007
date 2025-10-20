<?php

// database/migrations/*_create_catalog_banners_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalog_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_path')->nullable(); 
            $table->integer('order')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_banners');
    }
};