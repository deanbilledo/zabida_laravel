<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // PeaceWorks and Knowledge Products — the PDF archive (magazine issues,
    // briefs, reports). Grows over time via admin upload.
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->default('Knowledge Product'); // e.g. Magazine, Knowledge Product, Report
            $table->string('file_path'); // stored PDF, private disk
            $table->string('cover_image')->nullable(); // optional thumbnail
            $table->unsignedBigInteger('file_size')->default(0); // bytes, shown in UI
            $table->date('published_at');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
