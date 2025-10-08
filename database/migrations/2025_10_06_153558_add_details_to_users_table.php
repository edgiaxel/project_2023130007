<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // General User/Renter Info
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_picture')->nullable(); // For User/Renter public profile pic
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'address', 'profile_picture']);
        });
    }
};