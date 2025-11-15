# Create all remaining migrations
$migrations = @(
    "create_cuisines_table",
    "create_recipes_table",
    "create_ingredients_table",
    "create_recipe_ingredients_table",
    "create_steps_table",
    "create_tags_table",
    "create_recipe_tags_table",
    "create_ai_suggestions_table",
    "create_ai_suggestion_recipes_table",
    "create_ingredient_images_table",
    "create_detected_ingredients_table",
    "create_favorites_table",
    "create_recipe_likes_table",
    "create_meal_plans_table",
    "create_meal_plan_recipes_table",
    "create_feedback_table",
    "create_admins_table",
    "create_analytics_events_table",
    "create_subscriptions_table",
    "create_notifications_table",
    "create_recipe_shares_table",
    "create_recipe_comments_table",
    "create_comment_likes_table"
)

foreach ($migration in $migrations) {
    php artisan make:migration $migration
}
