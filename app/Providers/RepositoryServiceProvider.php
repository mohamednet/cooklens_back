<?php

namespace App\Providers;

use App\Features\Auth\Repositories\UserRepository;
use App\Features\MealPlans\Repositories\MealPlanRepository;
use App\Features\Recipes\Repositories\IngredientRepository;
use App\Features\Recipes\Repositories\RecipeRepository;
use App\Features\Social\Services\CommentRepository;
use App\Features\Social\Services\FavoriteRepository;
use App\Features\Subscriptions\Repositories\SubscriptionRepository;
use App\Repositories\Contracts\CommentRepositoryInterface;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Repositories\Contracts\IngredientRepositoryInterface;
use App\Repositories\Contracts\MealPlanRepositoryInterface;
use App\Repositories\Contracts\RecipeRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RecipeRepositoryInterface::class, RecipeRepository::class);
        $this->app->bind(IngredientRepositoryInterface::class, IngredientRepository::class);
        $this->app->bind(FavoriteRepositoryInterface::class, FavoriteRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(MealPlanRepositoryInterface::class, MealPlanRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, class-string>
     */
    public function provides(): array
    {
        return [
            UserRepositoryInterface::class,
            RecipeRepositoryInterface::class,
            IngredientRepositoryInterface::class,
            FavoriteRepositoryInterface::class,
            CommentRepositoryInterface::class,
            MealPlanRepositoryInterface::class,
            SubscriptionRepositoryInterface::class,
        ];
    }
}
