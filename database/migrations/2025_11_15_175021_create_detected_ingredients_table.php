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
        Schema::create('detected_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_image_id')->constrained('ingredient_images')->onDelete('cascade');
            $table->foreignId('ingredient_id')->nullable()->constrained('ingredients')->onDelete('set null');
            $table->string('detected_name', 150);
            $table->decimal('confidence', 5, 2)->comment('AI confidence score 0-100');
            $table->timestamps();
            
            // Indexes
            $table->index('ingredient_image_id');
            $table->index('ingredient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detected_ingredients');
    }
};
