<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetectedIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_image_id',
        'ingredient_id',
        'detected_name',
        'confidence',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:2',
        ];
    }

    public function ingredientImage()
    {
        return $this->belongsTo(IngredientImage::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
