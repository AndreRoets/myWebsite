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
            $table->string('dawn_image')->nullable()->after('hero_image');
            $table->string('noon_image')->nullable()->after('dawn_image');
            $table->string('dusk_image')->nullable()->after('noon_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', fn (Blueprint $table) => $table->dropColumn(['dawn_image', 'noon_image', 'dusk_image']));
    }
};