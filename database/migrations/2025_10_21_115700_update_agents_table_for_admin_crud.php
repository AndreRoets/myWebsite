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
        Schema::table('agents', function (Blueprint $table) {
            // The columns to drop.
            $columns = ['name', 'title', 'email', 'description', 'phone', 'image'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('agents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
