<?php

namespace App\Features\Recipes\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'prep_time' => ['required', 'integer', 'min:0'],
            'cook_time' => ['required', 'integer', 'min:0'],
            'servings' => ['required', 'integer', 'min:1'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'category_id' => ['required', 'exists:recipe_categories,id'],
            'cuisine_id' => ['nullable', 'exists:cuisines,id'],
            'calories' => ['nullable', 'integer', 'min:0'],
            'protein' => ['nullable', 'numeric', 'min:0'],
            'carbs' => ['nullable', 'numeric', 'min:0'],
            'fat' => ['nullable', 'numeric', 'min:0'],
            'fiber' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'video' => ['nullable', 'mimes:mp4,mov,avi,mpeg', 'max:102400'],
            'image' => ['nullable', 'image', 'max:5120'], // 5MB
            'video_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Recipe title is required.',
            'description.required' => 'Recipe description is required.',
            'prep_time.required' => 'Preparation time is required.',
            'cook_time.required' => 'Cooking time is required.',
            'servings.required' => 'Number of servings is required.',
            'difficulty.required' => 'Difficulty level is required.',
            'difficulty.in' => 'Difficulty must be easy, medium, or hard.',
            'category_id.required' => 'Recipe category is required.',
            'category_id.exists' => 'Selected category does not exist.',
        ];
    }
}
