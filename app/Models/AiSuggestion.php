<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ingredients_list',
    ];

    protected function casts(): array
    {
        return [
            'ingredients_list' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'ai_suggestion_recipes')
            ->withPivot('match_percentage')
            ->withTimestamps();
    }

    public function aiSuggestionRecipes()
    {
        return $this->hasMany(AiSuggestionRecipe::class);
    }
}
