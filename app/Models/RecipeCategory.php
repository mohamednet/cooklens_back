<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon_url'];

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'category_id');
    }
}
