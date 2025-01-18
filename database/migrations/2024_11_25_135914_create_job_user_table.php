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
        Schema::create('job_user', function (Blueprint $table) {
            $table->integer('job_id');
            $table->integer('user_id');
            $table->longText('data')->nullable();
            $table->dateTime('applied_at')->nullable();
            $table->dateTime('result_at')->nullable();
            $table->string('accesscode')->nullable();
            $table->string('feedback')->nullable();
            $table->string('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_user');
    }
};
