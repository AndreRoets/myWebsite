<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('city')->nullable()->after('region');
            $table->text('excerpt')->nullable()->after('description');
            $table->json('dawn_images')->nullable()->after('images_json');
            $table->json('noon_images')->nullable()->after('dawn_images');
            $table->json('dusk_images')->nullable()->after('noon_images');
            $table->json('gallery_images')->nullable()->after('dusk_images');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['city', 'excerpt', 'dawn_images', 'noon_images', 'dusk_images', 'gallery_images']);
        });
    }
};
