<?php
// FILE: database/migrations/2025_12_14_170003_add_soft_deletes_to_catalog_banners_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('catalog_banners', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('catalog_banners', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};