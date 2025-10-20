<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('costumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('name');
            $table->string('series');
            $table->string('size');
            $table->string('condition');
            $table->unsignedBigInteger('price_per_day');
            $table->integer('stock')->default(1);
            $table->string('main_image_path');
            $table->boolean('is_approved')->default(false); 
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costumes');
    }
};