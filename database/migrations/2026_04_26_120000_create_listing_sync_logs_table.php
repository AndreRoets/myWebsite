<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');                 // sync | delete
            $table->string('external_id')->nullable();
            $table->unsignedSmallInteger('status_code');
            $table->unsignedInteger('latency_ms')->default(0);
            $table->json('request_body')->nullable();
            $table->json('response_body')->nullable();
            $table->string('error')->nullable();
            $table->timestamps();
            $table->index(['external_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_sync_logs');
    }
};
