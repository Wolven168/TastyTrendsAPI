<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasters', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('user_name');
            $table->string('email');
            $table->string('password');
            $table->string('user_type')->default('customer');
            $table->string('user_img')->nullable();
            $table->string('store_id')->nullable();
            $table->string('phone_num')->nullable();
            $table->string('student_num')->nullable();
            $table->rememberToken("remember_Me")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasters');
    }
};
