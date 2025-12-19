<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('costume_id')->constrained()->onDelete('cascade');
        $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Only 1 review per order
        $table->integer('rating'); // 1-5
        $table->text('comment')->nullable();
        $table->json('images')->nullable(); // Store array of paths
        $table->timestamps();
    });

    Schema::create('review_moderation_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('review_id')->constrained()->onDelete('cascade');
        $table->foreignId('renter_id')->constrained('users')->onDelete('cascade');
        $table->text('reason');
        $table->string('proof_image')->nullable();
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews_and_moderation_tables');
    }
};
