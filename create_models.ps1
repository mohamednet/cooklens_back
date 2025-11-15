# Create all remaining models
$models = @(
    "UserProvider",
    "UserDevice",
    "RecipeCategory",
    "Cuisine",
    "Recipe",
    "Ingredient",
    "RecipeIngredient",
    "Step",
    "Tag",
    "RecipeTag",
    "AiSuggestion",
    "AiSuggestionRecipe",
    "IngredientImage",
    "DetectedIngredient",
    "Favorite",
    "RecipeLike",
    "MealPlan",
    "MealPlanRecipe",
    "Feedback",
    "RecipeShare",
    "RecipeComment",
    "CommentLike",
    "Admin",
    "AnalyticsEvent",
    "Subscription",
    "Notification"
)

foreach ($model in $models) {
    php artisan make:model $model
}
