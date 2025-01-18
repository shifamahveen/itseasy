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
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('slug')->nullable();
            $table->string('client_slug')->default('super');
            $table->string('type')->nullable();
            $table->string('location')->nullable();
            $table->string('zone')->nullable();
            $table->string('zone_code')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_designation')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('rating')->nullable();
            $table->float('reviews')->nullable();
            $table->string('category')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->longtext('address')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colleges');
    }
};
