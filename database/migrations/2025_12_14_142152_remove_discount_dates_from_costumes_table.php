<?php
// FILE: database/migrations/2025_12_14_170001_remove_discount_dates_from_costumes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            $table->dropColumn(['discount_start_date', 'discount_end_date']);
        });
    }

    public function down(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            $table->dateTime('discount_start_date')->nullable()->after('discount_type');
            $table->dateTime('discount_end_date')->nullable()->after('discount_start_date');
        });
    }
};