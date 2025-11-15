<?php

namespace App\Features\Recipes\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StepResource extends JsonResource
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
            'step_number' => $this->step_number,
            'instruction' => $this->instruction,
            'duration' => $this->duration,
            'image_url' => $this->image_url,
            'video_url' => $this->video_url,
            'tips' => $this->tips,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
