<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_renter_stores_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('renter_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade'); // Links to Renter user
            $table->string('store_name')->unique();
            $table->text('description')->nullable();
            $table->string('store_logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renter_stores');
    }
};