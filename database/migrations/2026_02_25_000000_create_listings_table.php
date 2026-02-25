<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_id')->unique()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price'); // ZAR, stored as-is
            $table->string('suburb');
            $table->string('region')->nullable();
            $table->tinyInteger('beds')->default(0);
            $table->tinyInteger('baths')->default(0);
            $table->tinyInteger('garages')->default(0);
            $table->integer('size_m2')->nullable();
            $table->integer('erf_size_m2')->nullable();
            $table->string('property_type');
            $table->string('mandate_type')->nullable();
            $table->string('status')->default('active');
            $table->json('images_json')->nullable();
            $table->json('agent_json')->nullable();
            $table->json('agency_json')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
