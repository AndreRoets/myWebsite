<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->string('external_id')->nullable()->unique()->after('id');
            $table->string('photo_source_url')->nullable();
            $table->text('bio')->nullable();
            $table->string('agency_external_id')->nullable();
            $table->string('agency_name')->nullable();
            $table->string('agency_branch')->nullable();
            $table->string('created_via')->default('manual');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'external_id', 'photo_source_url', 'bio',
                'agency_external_id', 'agency_name', 'agency_branch', 'created_via',
            ]);
        });
    }
};
