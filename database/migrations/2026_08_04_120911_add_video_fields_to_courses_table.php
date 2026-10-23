<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Backfill any columns missing from older DB versions of the courses table
            if (!Schema::hasColumn('courses', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('courses', 'category')) {
                $table->string('category')->default('General');
            }
            if (!Schema::hasColumn('courses', 'level')) {
                $table->enum('level', ['Beginner', 'Intermediate', 'Advanced'])->default('Beginner');
            }
            if (!Schema::hasColumn('courses', 'language')) {
                $table->string('language')->default('English');
            }
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('courses', 'thumbnail')) {
                $table->string('thumbnail')->nullable();
            }
            if (!Schema::hasColumn('courses', 'is_published')) {
                $table->boolean('is_published')->default(true);
            }
            if (!Schema::hasColumn('courses', 'lessons_count')) {
                $table->integer('lessons_count')->default(0);
            }
            // New video fields
            if (!Schema::hasColumn('courses', 'video_type')) {
                $table->string('video_type')->nullable(); // 'upload' or 'link'
            }
            if (!Schema::hasColumn('courses', 'video_link')) {
                $table->string('video_link')->nullable(); // YouTube/external URL
            }
            if (!Schema::hasColumn('courses', 'video_path')) {
                $table->string('video_path')->nullable(); // uploaded file path
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            foreach (['video_type', 'video_link', 'video_path'] as $col) {
                if (Schema::hasColumn('courses', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
