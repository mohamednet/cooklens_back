<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Recipe;
use App\Models\RecipeComment;
use App\Models\User;
use App\Policies\AdminPolicy;
use App\Policies\CommentPolicy;
use App\Policies\RecipePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Recipe::class => RecipePolicy::class,
        RecipeComment::class => CommentPolicy::class,
        User::class => UserPolicy::class,
        Admin::class => AdminPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
