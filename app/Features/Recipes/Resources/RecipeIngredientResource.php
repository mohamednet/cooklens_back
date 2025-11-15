<?php

namespace App\Features\Recipes\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeIngredientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'recipe_id' => $this->recipe_id,
            'ingredient' => [
                'id' => $this->ingredient->id,
                'name' => $this->ingredient->name,
                'category' => $this->ingredient->category,
                'calories_per_100g' => $this->ingredient->calories_per_100g,
                'protein_per_100g' => $this->ingredient->protein_per_100g,
                'carbs_per_100g' => $this->ingredient->carbs_per_100g,
                'fat_per_100g' => $this->ingredient->fat_per_100g,
            ],
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'notes' => $this->notes,
            'order' => $this->order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
