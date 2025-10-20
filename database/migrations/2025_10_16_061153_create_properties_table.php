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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->string('status')->default('for_sale');
            $table->string('type')->default('house');
            $table->string('city')->nullable();
            $table->string('suburb')->nullable();
            $table->unsignedInteger('bedrooms')->nullable();
            $table->unsignedInteger('bathrooms')->nullable();
            $table->unsignedInteger('garages')->nullable();
            $table->unsignedInteger('floor_size')->nullable();
            $table->unsignedInteger('erf_size')->nullable();
            $table->string('hero_image')->nullable();
            $table->json('images')->nullable();
            $table->timestamp('listed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
