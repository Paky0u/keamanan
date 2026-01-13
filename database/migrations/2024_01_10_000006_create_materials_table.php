<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // pdf, image, video
            $table->integer('file_size');
            $table->timestamps();
            
            $table->index(['class_id', 'created_at']);
            $table->index(['user_id']);
            $table->index(['file_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};