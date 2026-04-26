<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('external_id')->nullable()->unique()->after('id');
            $table->string('listing_type')->nullable()->after('type');
            $table->string('mandate_type')->nullable()->after('listing_type');
            $table->string('category')->nullable()->after('mandate_type');

            $table->string('unit_number')->nullable();
            $table->string('street_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('complex_name')->nullable();
            $table->string('town')->nullable()->after('city');
            $table->string('region')->nullable()->after('town');
            $table->string('province')->nullable()->after('region');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->foreignId('suburb_id')->nullable()->constrained('suburbs')->nullOnDelete();
            $table->foreignId('town_id')->nullable()->constrained('towns')->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();

            $table->date('listed_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->json('gallery_images_json')->nullable();
            $table->json('dawn_images_json')->nullable();
            $table->json('noon_images_json')->nullable();
            $table->json('dusk_images_json')->nullable();
            $table->json('primary_images_json')->nullable();

            $table->string('youtube_video_id')->nullable();
            $table->string('matterport_id')->nullable();

            $table->json('features_json')->nullable();
            $table->json('agency_json')->nullable();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropConstrainedForeignId('suburb_id');
            $table->dropConstrainedForeignId('town_id');
            $table->dropConstrainedForeignId('region_id');
            $table->dropConstrainedForeignId('province_id');
            $table->dropColumn([
                'external_id', 'listing_type', 'mandate_type', 'category',
                'unit_number', 'street_number', 'street_name', 'complex_name',
                'town', 'region', 'province', 'postal_code', 'latitude', 'longitude',
                'listed_date', 'expiry_date', 'published_at', 'synced_at',
                'gallery_images_json', 'dawn_images_json', 'noon_images_json',
                'dusk_images_json', 'primary_images_json',
                'youtube_video_id', 'matterport_id',
                'features_json', 'agency_json',
            ]);
        });
    }
};
