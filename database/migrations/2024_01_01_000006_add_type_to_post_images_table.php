<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Lets a single manually-created post carry several media items, mixing
    // photos and video clips — not just one cover image.
    public function up(): void
    {
        Schema::table('post_images', function (Blueprint $table) {
            $table->enum('type', ['image', 'video'])->default('image')->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('post_images', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
