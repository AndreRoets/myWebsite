<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->string('created_via')->default('manual');
            $table->timestamps();
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true);
            $table->string('created_via')->default('manual');
            $table->timestamps();
            $table->unique(['province_id', 'slug']);
        });

        Schema::create('towns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_active')->default(true);
            $table->string('created_via')->default('manual');
            $table->timestamps();
            $table->unique(['region_id', 'slug']);
        });

        Schema::create('suburbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('town_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('created_via')->default('manual');
            $table->timestamps();
            $table->unique(['town_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suburbs');
        Schema::dropIfExists('towns');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('provinces');
    }
};
