<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body')->nullable();
            $table->string('image')->nullable(); // cover image, storage path
            $table->string('video_url')->nullable(); // downloaded local mp4 or FB CDN fallback
            $table->enum('source', ['manual', 'facebook'])->default('manual');
            $table->string('facebook_post_id')->nullable()->unique();
            $table->string('facebook_permalink')->nullable();
            $table->date('published_at');
            $table->timestamps();

            $table->index(['source', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
