<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecipeComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'recipe_id',
        'user_id',
        'parent_id',
        'comment',
        'likes_count',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'likes_count' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(RecipeComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(RecipeComment::class, 'parent_id');
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class, 'comment_id');
    }
}
