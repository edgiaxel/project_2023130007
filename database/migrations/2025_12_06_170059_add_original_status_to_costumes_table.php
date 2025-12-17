<?php
// FILE: database/migrations/2025_12_07_120016_add_original_status_to_costumes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            $table->string('original_status')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            $table->dropColumn('original_status');
        });
    }
};