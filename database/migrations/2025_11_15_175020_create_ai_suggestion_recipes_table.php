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
        Schema::create('ai_suggestion_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_suggestion_id')->constrained('ai_suggestions')->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->decimal('match_percentage', 5, 2)->comment('How well recipe matches ingredients');
            $table->timestamps();
            
            // Indexes
            $table->index('ai_suggestion_id');
            $table->index('recipe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_suggestion_recipes');
    }
};
