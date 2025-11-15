<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'country',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }

    // Relationships
    public function providers()
    {
        return $this->hasMany(UserProvider::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'created_by');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function recipeLikes()
    {
        return $this->hasMany(RecipeLike::class);
    }

    public function comments()
    {
        return $this->hasMany(RecipeComment::class);
    }

    public function mealPlans()
    {
        return $this->hasMany(MealPlan::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function aiSuggestions()
    {
        return $this->hasMany(AiSuggestion::class);
    }

    public function ingredientImages()
    {
        return $this->hasMany(IngredientImage::class);
    }
}
