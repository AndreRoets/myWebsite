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
        Schema::table('agents', function (Blueprint $table) {
            if (!Schema::hasColumn('agents', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('agents', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('agents', 'email')) {
                $table->string('email')->unique();
            }
            if (!Schema::hasColumn('agents', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('agents', 'phone')) {
                $table->string('phone', 20);
            }
            if (!Schema::hasColumn('agents', 'image')) {
                $table->string('image')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For SQLite, it's safer to drop the index before the column.
        // We can use a separate Schema block to ensure the index is dropped first.
        Schema::table('agents', function (Blueprint $table) {
            $table->dropUnique('agents_email_unique');
        });
        Schema::table('agents', fn (Blueprint $table) => $table->dropColumn(['name', 'title', 'email', 'description', 'phone', 'image']));
    }
};
