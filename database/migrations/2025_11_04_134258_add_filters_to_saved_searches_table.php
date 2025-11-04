<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No-op if table/column already present
        if (Schema::hasTable('saved_searches') && !Schema::hasColumn('saved_searches', 'filters')) {
            Schema::table('saved_searches', function (Blueprint $table) {
                $table->json('filters')->nullable()->after('name'); // SQLite stores as TEXT; fine for cast
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('saved_searches') && Schema::hasColumn('saved_searches', 'filters')) {
            Schema::table('saved_searches', function (Blueprint $table) {
                $table->dropColumn('filters');
            });
        }
    }
};

