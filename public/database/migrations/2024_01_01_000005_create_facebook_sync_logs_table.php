<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Every sync run (manual or cron) logs its outcome here so the admin
    // panel can show real success/error feedback instead of guessing.
    public function up(): void
    {
        Schema::create('facebook_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['success', 'error'])->default('success');
            $table->unsignedInteger('posts_created')->default(0);
            $table->text('message')->nullable();
            $table->timestamp('ran_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_sync_logs');
    }
};
