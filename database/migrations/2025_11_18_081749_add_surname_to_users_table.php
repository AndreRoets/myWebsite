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
        Schema::table('users', function (Blueprint $table) {
            // Add the new 'surname' column after the 'name' column.
            // It's nullable() to avoid issues with existing users who don't have a surname yet.
            $table->string('surname')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // This will remove the column if we ever need to roll back the migration.
            $table->dropColumn('surname');
        });
    }
};

