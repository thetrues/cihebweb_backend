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
        Schema::create('gallery', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('category')->nullable();
            $table->string('image_path')->nullable();
            $table->string('video_path')->nullable();
            $table->text('description')->nullable()->change();
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gallery');
    }
};
