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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->integer('rating')->comment('1-5 stars');
            $table->text('review')->nullable();
            $table->boolean('is_verified')->default(false)->comment('User actually made the recipe');
            $table->unsignedBigInteger('helpful_count')->default(0);
            $table->timestamps();
            
            // Unique constraint and indexes
            $table->unique(['user_id', 'recipe_id']);
            $table->index('user_id');
            $table->index('recipe_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
