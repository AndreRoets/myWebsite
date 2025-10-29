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
        Schema::table('properties', function (Blueprint $table) {
            $table->json('dawn_image')->nullable()->change();
            $table->json('noon_image')->nullable()->change();
            $table->json('dusk_image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', fn (Blueprint $table) => $table->string('dawn_image')->nullable()->change());
        Schema::table('properties', fn (Blueprint $table) => $table->string('noon_image')->nullable()->change());
        Schema::table('properties', fn (Blueprint $table) => $table->string('dusk_image')->nullable()->change());
    }
};