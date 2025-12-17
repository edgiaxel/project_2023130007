<?php
// FILE: database/migrations/2025_12_14_170000_add_discount_fields_to_costumes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            $table->decimal('discount_value', 8, 2)->nullable()->after('price_per_day');
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable()->after('discount_value');
            $table->dateTime('discount_start_date')->nullable()->after('discount_type');
            $table->dateTime('discount_end_date')->nullable()->after('discount_start_date');
            $table->boolean('is_discount_active')->default(false)->after('discount_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('costumes', function (Blueprint $table) {
            $table->dropColumn(['discount_value', 'discount_type', 'discount_start_date', 'discount_end_date', 'is_discount_active']);
        });
    }
};