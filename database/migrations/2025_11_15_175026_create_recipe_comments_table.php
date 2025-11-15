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
        Schema::create('recipe_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('recipe_comments')->onDelete('cascade')->comment('For nested comments');
            $table->text('comment');
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('recipe_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('is_approved');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_comments');
    }
};
