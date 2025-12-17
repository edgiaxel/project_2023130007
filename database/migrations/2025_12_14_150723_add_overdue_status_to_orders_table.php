
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Change the existing ENUM column to include 'overdue'
            $table->enum('status', ['waiting', 'confirmed', 'borrowed', 'returned', 'completed', 'rejected', 'overdue'])
                  ->default('waiting')
                  ->change(); // Use change() to modify existing column definition
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert back to the old ENUM definition
            $table->enum('status', ['waiting', 'confirmed', 'borrowed', 'returned', 'completed', 'rejected'])
                  ->default('waiting')
                  ->change();
        });
    }
};