<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_url',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detectedIngredients()
    {
        return $this->hasMany(DetectedIngredient::class);
    }
}
