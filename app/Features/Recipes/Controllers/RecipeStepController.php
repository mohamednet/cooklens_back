<?php

namespace App\Features\Recipes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Step;
use App\Services\FileUploadService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeStepController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Add step to recipe.
     */
    public function store(Request $request, Recipe $recipe): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        $validated = $request->validate([
            'instruction' => ['required', 'string'],
            'step_number' => ['required', 'integer', 'min:1'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
            'video_url' => ['nullable', 'url'],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_url'] = $this->fileUploadService->upload(
                $request->file('image'),
                'recipes/steps'
            );
            unset($validated['image']);
        }

        $validated['recipe_id'] = $recipe->id;
        $step = Step::create($validated);

        return $this->createdResponse(
            $step,
            'Step added successfully'
        );
    }

    /**
     * Update step.
     */
    public function update(Request $request, Recipe $recipe, Step $step): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        // Verify step belongs to recipe
        if ($step->recipe_id !== $recipe->id) {
            return $this->errorResponse('Step does not belong to this recipe', 400);
        }

        $validated = $request->validate([
            'instruction' => ['sometimes', 'string'],
            'step_number' => ['sometimes', 'integer', 'min:1'],
            'duration' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
            'video_url' => ['nullable', 'url'],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($step->image_url) {
                $this->fileUploadService->delete($step->image_url);
            }

            $validated['image_url'] = $this->fileUploadService->upload(
                $request->file('image'),
                'recipes/steps'
            );
            unset($validated['image']);
        }

        $step->update($validated);

        return $this->successResponse(
            $step->fresh(),
            'Step updated successfully'
        );
    }

    /**
     * Delete step.
     */
    public function destroy(Request $request, Recipe $recipe, Step $step): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        // Verify step belongs to recipe
        if ($step->recipe_id !== $recipe->id) {
            return $this->errorResponse('Step does not belong to this recipe', 400);
        }

        // Delete image if exists
        if ($step->image_url) {
            $this->fileUploadService->delete($step->image_url);
        }

        $step->delete();

        return $this->successResponse(null, 'Step deleted successfully');
    }

    /**
     * Reorder steps.
     */
    public function reorder(Request $request, Recipe $recipe): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('update', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to modify this recipe');
        }

        $validated = $request->validate([
            'steps' => ['required', 'array'],
            'steps.*.id' => ['required', 'exists:steps,id'],
            'steps.*.step_number' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($validated['steps'] as $stepData) {
            Step::where('id', $stepData['id'])
                ->where('recipe_id', $recipe->id)
                ->update(['step_number' => $stepData['step_number']]);
        }

        return $this->successResponse(
            $recipe->fresh('steps')->steps,
            'Steps reordered successfully'
        );
    }
}
