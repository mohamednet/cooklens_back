<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'category_id',
        'cuisine_id',
        'title',
        'slug',
        'description',
        'image_url',
        'video_url',
        'prep_time',
        'cook_time',
        'servings',
        'difficulty',
        'calories',
        'nutrition_info',
        'status',
        'published_at',
        'views_count',
        'likes_count',
        'favorites_count',
        'average_rating',
    ];

    protected function casts(): array
    {
        return [
            'nutrition_info' => 'array',
            'published_at' => 'datetime',
            'views_count' => 'integer',
            'likes_count' => 'integer',
            'favorites_count' => 'integer',
            'average_rating' => 'decimal:2',
        ];
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class);
    }

    public function cuisine()
    {
        return $this->belongsTo(Cuisine::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }

    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('step_number');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'recipe_tags')->withTimestamps();
    }

    public function likes()
    {
        return $this->hasMany(RecipeLike::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function comments()
    {
        return $this->hasMany(RecipeComment::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function shares()
    {
        return $this->hasMany(RecipeShare::class);
    }

    public function mealPlanRecipes()
    {
        return $this->hasMany(MealPlanRecipe::class);
    }
}
