<?php

namespace App\Features\Recipes\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('recipe'));
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'prep_time' => ['sometimes', 'integer', 'min:0'],
            'cook_time' => ['sometimes', 'integer', 'min:0'],
            'servings' => ['sometimes', 'integer', 'min:1'],
            'difficulty' => ['sometimes', 'in:easy,medium,hard'],
            'category_id' => ['sometimes', 'exists:recipe_categories,id'],
            'cuisine_id' => ['nullable', 'exists:cuisines,id'],
            'calories' => ['nullable', 'integer', 'min:0'],
            'protein' => ['nullable', 'numeric', 'min:0'],
            'carbs' => ['nullable', 'numeric', 'min:0'],
            'fat' => ['nullable', 'numeric', 'min:0'],
            'fiber' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
            'video_url' => ['nullable', 'url'],
        ];
    }
}
