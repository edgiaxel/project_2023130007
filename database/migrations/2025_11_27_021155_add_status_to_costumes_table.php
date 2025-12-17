<?php
// database/migrations/XXXX_XX_XX_XXXXXX_add_status_to_costumes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            // Drop the old boolean field
            $table->dropColumn('is_approved'); 
            
            // Add the new ENUM status field (0 = pending, 1 = approved, 2 = rejected)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('main_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->boolean('is_approved')->default(false)->after('main_image_path');
        });
    }
};