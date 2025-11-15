<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_tags')->withTimestamps();
    }
}
