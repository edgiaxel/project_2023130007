<?php
// FILE: database/migrations/2025_12_06_072917_drop_main_image_from_costumes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            // Check if the column exists before dropping (important for development stability)
            if (Schema::hasColumn('costumes', 'main_image_path')) {
                $table->dropColumn('main_image_path'); // 💥 DROPPING THE OLD COLUMN
            }
        });
    }

    public function down(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            // Restore the column on rollback
            $table->string('main_image_path')->nullable()->after('stock');
        });
    }
};