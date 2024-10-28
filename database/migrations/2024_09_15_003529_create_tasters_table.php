<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTastersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the tasters table
        Schema::create('tasters', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique()->index(); // Ensure user_id is unique and indexed
            $table->string('user_name');
            $table->string('email')->unique(); // Ensure email is unique
            $table->string('password');
            $table->string('user_type')->default('customer'); // Default to 'customer'
            $table->longText('user_image')->nullable();
            $table->string('store_id')->nullable();
            $table->string('phone_num')->nullable();
            $table->json('favorites')->nullable();
            $table->string('student_num')->nullable();
            $table->rememberToken(); // You don't need to pass 'nullable' to rememberToken
            $table->timestamps();
        });

        // Create the password_resets table
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index(); // Index for faster lookups
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('tasters');
    }
}
