<?php

// database/migrations/*_create_global_discounts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('global_discounts', function (Blueprint $table) {
            $table->id();
            // Store discount as a percentage (e.g., 0.15 for 15%)
            $table->decimal('rate', 4, 2)->default(0.00);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_discounts');
    }
};