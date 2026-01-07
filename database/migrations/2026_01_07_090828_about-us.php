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
        Schema::create('about_us', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('video_path')->nullable();
            $table->string('mission')->nullable();
            $table->string('vision')->nullable();
            $table->string('values')->nullable();
            $table->integer('established_year')->nullable();
            $table->string('founder')->nullable();
            $table->string('location')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('social_media_links')->nullable();
            $table->string('awards')->nullable();
            $table->string('team_members')->nullable();
            $table->string('regions')->nullable();
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
