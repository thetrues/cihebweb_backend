<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * {"title":"Test","department":"Test","location":"Dar es","type":"Full-time","salary":"","experience":"4","description":"test","responsibilities":"test","requirements":"test","status":"open"}
     */
    public function up(): void
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department');
            $table->string('location');
            $table->string('type');
            $table->string('salary')->nullable();
            $table->string('experience');
            $table->text('description');
            $table->text('responsibilities');
            $table->text('requirements');
            $table->dateTime('closing_date')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
