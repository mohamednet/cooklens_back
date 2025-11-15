<?php

namespace App\Features\Recipes\Controllers;

use App\Features\Recipes\Requests\StoreRecipeRequest;
use App\Features\Recipes\Requests\UpdateRecipeRequest;
use App\Features\Recipes\Resources\RecipeListResource;
use App\Features\Recipes\Resources\RecipeResource;
use App\Features\Recipes\Services\RecipeService;
use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected RecipeService $recipeService,
        protected \App\Services\FileUploadService $fileUploadService
    ) {}

    /**
     * List all recipes (public).
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status', 'published');
        $sort = $request->input('sort', 'latest');

        $query = Recipe::with(['category', 'cuisine', 'creator'])
            ->where('status', $status);

        // Apply sorting
        $query = match($sort) {
            'popular' => $query->orderBy('views_count', 'desc'),
            'liked' => $query->orderBy('likes_count', 'desc'),
            'rated' => $query->orderBy('average_rating', 'desc'),
            'favorited' => $query->orderBy('favorites_count', 'desc'),
            default => $query->latest('published_at'),
        };

        $recipes = $query->paginate($perPage);

        return $this->successResponse(
            RecipeListResource::collection($recipes)->response()->getData(true)
        );
    }

    /**
     * Store a new recipe.
     */
    public function store(StoreRecipeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            $urls = $this->fileUploadService->uploadImage(
                $request->file('image'),
                'recipes/images',
                [
                    'thumbnail' => ['width' => 300, 'height' => 300],
                    'medium' => ['width' => 600, 'height' => 400],
                    'large' => ['width' => 1200, 'height' => 800],
                ]
            );
            $data['image_url'] = $urls['original'];
            $data['thumbnail_url'] = $urls['thumbnail'];
        }

        // Handle video upload
        if ($request->hasFile('video')) {
            $data['video_url'] = $this->fileUploadService->uploadVideo(
                $request->file('video'),
                'recipes/videos'
            );
        }

        $recipe = $this->recipeService->create($data);

        return $this->createdResponse(
            new RecipeResource($recipe->load(['category', 'cuisine', 'creator'])),
            'Recipe created successfully'
        );
    }

    /**
     * Show recipe details.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $recipe = Recipe::with([
            'category',
            'cuisine',
            'creator',
            'ingredients',
            'steps' => fn($q) => $q->orderBy('step_number'),
            'tags'
        ])->where('slug', $slug)->firstOrFail();

        // Check authorization for draft recipes using policy
        if ($recipe->status === 'draft') {
            $user = $request->user();
            if (!$user || !$user->can('view', $recipe)) {
                return $this->forbiddenResponse('This recipe is not published yet');
            }
        }

        // Increment views
        $this->recipeService->incrementViews($recipe->id);

        return $this->successResponse(
            new RecipeResource($recipe)
        );
    }

    /**
     * Update recipe.
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe): JsonResponse
    {
        $updated = $this->recipeService->update($recipe->id, $request->validated());

        if (!$updated) {
            return $this->errorResponse('Failed to update recipe', 500);
        }

        return $this->successResponse(
            new RecipeResource($recipe->fresh(['category', 'cuisine', 'creator'])),
            'Recipe updated successfully'
        );
    }

    /**
     * Delete recipe.
     */
    public function destroy(Request $request, Recipe $recipe): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('delete', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to delete this recipe');
        }

        // Delete recipe images
        if ($recipe->image_url) {
            $this->fileUploadService->delete($recipe->image_url);
        }
        if ($recipe->thumbnail_url) {
            $this->fileUploadService->delete($recipe->thumbnail_url);
        }
        if ($recipe->video_url) {
            $this->fileUploadService->delete($recipe->video_url);
        }

        // Delete step images
        foreach ($recipe->steps as $step) {
            if ($step->image_url) {
                $this->fileUploadService->delete($step->image_url);
            }
        }

        // Soft delete recipe (cascade handled by database)
        $this->recipeService->delete($recipe->id);

        return $this->successResponse(null, 'Recipe deleted successfully');
    }

    /**
     * Publish recipe.
     */
    public function publish(Request $request, Recipe $recipe): JsonResponse
    {
        // Check authorization
        if ($request->user()->cannot('publish', $recipe)) {
            return $this->forbiddenResponse('You are not authorized to publish this recipe');
        }

        // Validate recipe completeness
        $errors = [];
        
        if (!$recipe->image_url) {
            $errors[] = 'Recipe must have a cover image';
        }
        
        if ($recipe->ingredients()->count() === 0) {
            $errors[] = 'Recipe must have at least one ingredient';
        }
        
        if ($recipe->steps()->count() === 0) {
            $errors[] = 'Recipe must have at least one cooking step';
        }

        if (!empty($errors)) {
            return $this->errorResponse('Recipe is incomplete: ' . implode(', ', $errors), 422);
        }

        $this->recipeService->publish($recipe->id);

        return $this->successResponse(
            new RecipeResource($recipe->fresh()),
            'Recipe published successfully'
        );
    }

    /**
     * Get user's own recipes.
     */
    public function myRecipes(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');

        $query = Recipe::with(['category', 'cuisine'])
            ->where('created_by', $request->user()->id);

        if ($status) {
            $query->where('status', $status);
        }

        $recipes = $query->latest()->paginate($perPage);

        return $this->successResponse(
            RecipeListResource::collection($recipes)->response()->getData(true)
        );
    }
}
