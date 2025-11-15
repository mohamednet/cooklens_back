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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('recipe_categories')->onDelete('restrict');
            $table->foreignId('cuisine_id')->nullable()->constrained('cuisines')->onDelete('set null');
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('description');
            $table->string('image_url', 255)->nullable();
            $table->string('video_url', 255)->nullable();
            $table->integer('prep_time')->comment('in minutes');
            $table->integer('cook_time')->comment('in minutes');
            $table->integer('servings')->default(1);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('calories')->nullable();
            $table->json('nutrition_info')->nullable()->comment('protein, carbs, fats, etc.');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('favorites_count')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('created_by');
            $table->index('category_id');
            $table->index('cuisine_id');
            $table->index('slug');
            $table->index('status');
            $table->index('difficulty');
            $table->index('published_at');
            $table->index('deleted_at');
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
