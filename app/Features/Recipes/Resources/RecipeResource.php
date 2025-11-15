<?php

namespace App\Features\Recipes\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'prep_time' => $this->prep_time,
            'cook_time' => $this->cook_time,
            'total_time' => $this->prep_time + $this->cook_time,
            'servings' => $this->servings,
            'difficulty' => $this->difficulty,
            'status' => $this->status,
            'image_url' => $this->image_url,
            'video_url' => $this->video_url,
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'favorites_count' => $this->favorites_count,
            'average_rating' => $this->average_rating ? round($this->average_rating, 1) : null,
            'ratings_count' => $this->ratings_count,
            
            // Nutrition
            'nutrition' => [
                'calories' => $this->calories,
                'protein' => $this->protein,
                'carbs' => $this->carbs,
                'fat' => $this->fat,
                'fiber' => $this->fiber,
            ],
            
            // Relationships
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            
            'cuisine' => $this->whenLoaded('cuisine', fn() => [
                'id' => $this->cuisine->id,
                'name' => $this->cuisine->name,
            ]),
            
            'user' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'avatar_url' => $this->creator->avatar_url,
            ]),
            
            'ingredients' => $this->whenLoaded('ingredients', fn() => 
                $this->ingredients->map(fn($ingredient) => [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'quantity' => $ingredient->pivot->quantity,
                    'unit' => $ingredient->pivot->unit,
                    'notes' => $ingredient->pivot->notes,
                ])
            ),
            
            'steps' => $this->whenLoaded('steps', fn() => 
                $this->steps->map(fn($step) => [
                    'id' => $step->id,
                    'step_number' => $step->step_number,
                    'instruction' => $step->instruction,
                    'image_url' => $step->image_url,
                    'video_url' => $step->video_url,
                    'duration' => $step->duration,
                ])
            ),
            
            'tags' => $this->whenLoaded('tags', fn() => 
                $this->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'type' => $tag->type,
                ])
            ),
            
            // User interaction status
            'is_liked' => $this->when($request->user(), fn() => 
                $this->likes()->where('user_id', $request->user()->id)->exists()
            ),
            
            'is_favorited' => $this->when($request->user(), fn() => 
                $this->favorites()->where('user_id', $request->user()->id)->exists()
            ),
            
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
