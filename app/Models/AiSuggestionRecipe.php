<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSuggestionRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_suggestion_id',
        'recipe_id',
        'match_percentage',
    ];

    protected function casts(): array
    {
        return [
            'match_percentage' => 'decimal:2',
        ];
    }

    public function aiSuggestion()
    {
        return $this->belongsTo(AiSuggestion::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
