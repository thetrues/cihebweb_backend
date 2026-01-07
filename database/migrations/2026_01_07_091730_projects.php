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
        Schema::create('projects', function (Blueprint $table) {
           $table->string('title')->nullable();
           $table->string('subtitle')->nullable();
           $table->string('image_path')->nullable();
           $table->string('video_path')->nullable();
           $table->text('short_description')->nullable();
           $table->text('detailed_description')->nullable();
           $table->string('location')->nullable();
           $table->string('partner_organizations')->nullable();
           $table->date('start_date')->nullable();
           $table->date('end_date')->nullable();
           $table->string('funding_source')->nullable();
           $table->decimal('budget', 15, 2)->nullable();
           $table->string('status')->nullable();
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
