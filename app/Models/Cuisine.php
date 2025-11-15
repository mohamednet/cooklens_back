<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuisine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'region',
        'description',
        'image_url',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}
