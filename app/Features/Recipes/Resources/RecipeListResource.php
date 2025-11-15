<?php

namespace App\Features\Recipes\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->when(
                strlen($this->description) > 150,
                substr($this->description, 0, 150) . '...',
                $this->description
            ),
            'prep_time' => $this->prep_time,
            'cook_time' => $this->cook_time,
            'total_time' => $this->prep_time + $this->cook_time,
            'servings' => $this->servings,
            'difficulty' => $this->difficulty,
            'status' => $this->status,
            'image_url' => $this->image_url,
            'views_count' => $this->views_count,
            'likes_count' => $this->likes_count,
            'favorites_count' => $this->favorites_count,
            'average_rating' => $this->average_rating ? round($this->average_rating, 1) : null,
            'ratings_count' => $this->ratings_count,
            
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ]),
            
            'cuisine' => $this->whenLoaded('cuisine', fn() => [
                'id' => $this->cuisine->id,
                'name' => $this->cuisine->name,
            ]),
            
            'user' => $this->whenLoaded('creator', fn() => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
