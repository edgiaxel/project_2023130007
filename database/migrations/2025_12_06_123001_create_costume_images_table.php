<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('costume_images', function (Blueprint $table) {
            $table->id();
            // Foreign key linking back to the costume
            $table->foreignId('costume_id')->constrained()->onDelete('cascade');
            $table->string('image_path'); // Path to the uploaded image file
            $table->integer('order')->default(0); // For display ordering
            $table->timestamps();

            // Index for faster retrieval by costume
            $table->index('costume_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costume_images');
    }
};