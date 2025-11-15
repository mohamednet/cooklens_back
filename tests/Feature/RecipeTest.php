<?php

namespace Tests\Feature;

use App\Models\Cuisine;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected RecipeCategory $category;
    protected Cuisine $cuisine;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = RecipeCategory::create([
            'name' => 'Dinner',
            'slug' => 'dinner',
        ]);
        $this->cuisine = Cuisine::create([
            'name' => 'Italian',
            'slug' => 'italian',
        ]);
    }

    public function test_user_can_create_recipe(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/recipes', [
                'title' => 'Test Recipe',
                'description' => 'Test description',
                'prep_time' => 10,
                'cook_time' => 20,
                'servings' => 4,
                'difficulty' => 'easy',
                'category_id' => $this->category->id,
                'cuisine_id' => $this->cuisine->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'status',
                    'category',
                    'cuisine',
                ]
            ]);

        $this->assertDatabaseHas('recipes', [
            'title' => 'Test Recipe',
            'slug' => 'test-recipe',
            'status' => 'draft',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_guest_cannot_create_recipe(): void
    {
        $response = $this->postJson('/api/recipes', [
            'title' => 'Test Recipe',
            'description' => 'Test description',
            'prep_time' => 10,
            'cook_time' => 20,
            'servings' => 4,
            'difficulty' => 'easy',
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_recipe_creation_requires_validation(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/recipes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'prep_time', 'cook_time', 'servings', 'difficulty', 'category_id']);
    }

    public function test_user_can_list_published_recipes(): void
    {
        Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);

        $response = $this->getJson('/api/recipes');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    public function test_user_can_view_published_recipe(): void
    {
        $recipe = Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->getJson("/api/recipes/{$recipe->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'status' => 'published',
                ]
            ]);
    }

    public function test_owner_can_view_draft_recipe(): void
    {
        $recipe = Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/recipes/{$recipe->slug}");

        $response->assertStatus(200);
    }

    public function test_guest_cannot_view_draft_recipe(): void
    {
        $recipe = Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);

        $response = $this->getJson("/api/recipes/{$recipe->slug}");

        $response->assertStatus(403);
    }

    public function test_user_can_update_own_recipe(): void
    {
        $recipe = Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/recipes/{$recipe->id}", [
                'title' => 'Updated Title',
                'servings' => 6,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'title' => 'Updated Title',
            'servings' => 6,
        ]);
    }

    public function test_user_cannot_update_others_recipe(): void
    {
        $otherUser = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'created_by' => $otherUser->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/recipes/{$recipe->id}", [
                'title' => 'Hacked Title',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_publish_recipe(): void
    {
        $recipe = Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/recipes/{$recipe->id}/publish");

        $response->assertStatus(200);

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'status' => 'published',
        ]);

        $recipe->refresh();
        $this->assertNotNull($recipe->published_at);
    }

    public function test_user_can_delete_own_recipe(): void
    {
        $recipe = Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/recipes/{$recipe->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('recipes', [
            'id' => $recipe->id,
        ]);
    }

    public function test_user_can_add_ingredients_to_recipe(): void
    {
        $recipe = Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $ingredient = Ingredient::create(['name' => 'Flour']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/recipes/{$recipe->id}/ingredients", [
                'ingredient_id' => $ingredient->id,
                'quantity' => 200,
                'unit' => 'grams',
                'notes' => 'all-purpose',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('recipe_ingredients', [
            'recipe_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => 200,
            'unit' => 'grams',
        ]);
    }

    public function test_user_can_add_steps_to_recipe(): void
    {
        $recipe = Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/recipes/{$recipe->id}/steps", [
                'instruction' => 'Boil water',
                'step_number' => 1,
                'duration' => 5,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('steps', [
            'recipe_id' => $recipe->id,
            'instruction' => 'Boil water',
            'step_number' => 1,
        ]);
    }

    public function test_user_can_search_recipes(): void
    {
        Recipe::factory()->create([
            'title' => 'Chocolate Cake',
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        Recipe::factory()->create([
            'title' => 'Vanilla Ice Cream',
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/recipes/search?query=chocolate');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    public function test_user_can_filter_recipes_by_difficulty(): void
    {
        Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'difficulty' => 'easy',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Recipe::factory()->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'difficulty' => 'hard',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/recipes/search?difficulty=easy');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    public function test_user_can_view_own_recipes(): void
    {
        Recipe::factory()->count(3)->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $otherUser = User::factory()->create();
        Recipe::factory()->create([
            'created_by' => $otherUser->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/my-recipes');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data.data');
    }

    public function test_pagination_works_correctly(): void
    {
        Recipe::factory()->count(25)->create([
            'created_by' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/recipes?per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data.data')
            ->assertJsonPath('data.total', 25)
            ->assertJsonPath('data.per_page', 10);
    }
}
