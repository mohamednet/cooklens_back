<?php

// This script generates complete model files for all CookLens models
// Run with: php generate_all_models.php

$models = [
    'RecipeCategory' => [
        'fillable' => ['name', 'slug', 'description', 'icon_url'],
        'casts' => [],
        'relationships' => [
            'recipes' => 'hasMany:Recipe'
        ]
    ],
    'Cuisine' => [
        'fillable' => ['name', 'slug', 'region', 'description', 'image_url'],
        'casts' => [],
        'relationships' => [
            'recipes' => 'hasMany:Recipe'
        ]
    ],
    'Ingredient' => [
        'fillable' => ['name', 'category', 'image_url'],
        'casts' => [],
        'relationships' => [
            'recipes' => 'belongsToMany:Recipe,recipe_ingredients,withPivot:quantity,unit',
            'recipeIngredients' => 'hasMany:RecipeIngredient'
        ]
    ],
    'RecipeIngredient' => [
        'fillable' => ['recipe_id', 'ingredient_id', 'quantity', 'unit'],
        'casts' => ['quantity' => 'float'],
        'relationships' => [
            'recipe' => 'belongsTo:Recipe',
            'ingredient' => 'belongsTo:Ingredient'
        ]
    ],
    'Step' => [
        'fillable' => ['recipe_id', 'step_number', 'instruction', 'image_url', 'video_url'],
        'casts' => ['step_number' => 'integer'],
        'relationships' => [
            'recipe' => 'belongsTo:Recipe'
        ]
    ],
    'Tag' => [
        'fillable' => ['name', 'slug', 'type'],
        'casts' => [],
        'relationships' => [
            'recipes' => 'belongsToMany:Recipe,recipe_tags'
        ]
    ],
    'RecipeTag' => [
        'fillable' => ['recipe_id', 'tag_id'],
        'casts' => [],
        'relationships' => [
            'recipe' => 'belongsTo:Recipe',
            'tag' => 'belongsTo:Tag'
        ]
    ],
    'AiSuggestion' => [
        'fillable' => ['user_id', 'ingredients_list'],
        'casts' => ['ingredients_list' => 'array'],
        'relationships' => [
            'user' => 'belongsTo:User',
            'recipes' => 'belongsToMany:Recipe,ai_suggestion_recipes,withPivot:match_percentage'
        ]
    ],
    'AiSuggestionRecipe' => [
        'fillable' => ['ai_suggestion_id', 'recipe_id', 'match_percentage'],
        'casts' => ['match_percentage' => 'decimal:2'],
        'relationships' => [
            'aiSuggestion' => 'belongsTo:AiSuggestion',
            'recipe' => 'belongsTo:Recipe'
        ]
    ],
    'IngredientImage' => [
        'fillable' => ['user_id', 'image_url', 'status'],
        'casts' => [],
        'relationships' => [
            'user' => 'belongsTo:User',
            'detectedIngredients' => 'hasMany:DetectedIngredient'
        ]
    ],
    'DetectedIngredient' => [
        'fillable' => ['ingredient_image_id', 'ingredient_id', 'detected_name', 'confidence'],
        'casts' => ['confidence' => 'decimal:2'],
        'relationships' => [
            'ingredientImage' => 'belongsTo:IngredientImage',
            'ingredient' => 'belongsTo:Ingredient'
        ]
    ],
    'Favorite' => [
        'fillable' => ['user_id', 'recipe_id'],
        'casts' => [],
        'relationships' => [
            'user' => 'belongsTo:User',
            'recipe' => 'belongsTo:Recipe'
        ]
    ],
    'RecipeLike' => [
        'fillable' => ['user_id', 'recipe_id'],
        'casts' => [],
        'relationships' => [
            'user' => 'belongsTo:User',
            'recipe' => 'belongsTo:Recipe'
        ]
    ],
    'MealPlan' => [
        'fillable' => ['user_id', 'name', 'start_date', 'end_date', 'total_calories'],
        'casts' => ['start_date' => 'date', 'end_date' => 'date', 'total_calories' => 'integer'],
        'traits' => ['SoftDeletes'],
        'relationships' => [
            'user' => 'belongsTo:User',
            'recipes' => 'belongsToMany:Recipe,meal_plan_recipes,withPivot:planned_date,meal_type',
            'mealPlanRecipes' => 'hasMany:MealPlanRecipe'
        ]
    ],
    'MealPlanRecipe' => [
        'fillable' => ['meal_plan_id', 'recipe_id', 'planned_date', 'meal_type'],
        'casts' => ['planned_date' => 'date'],
        'relationships' => [
            'mealPlan' => 'belongsTo:MealPlan',
            'recipe' => 'belongsTo:Recipe'
        ]
    ],
    'Feedback' => [
        'fillable' => ['user_id', 'recipe_id', 'rating', 'review', 'is_verified', 'helpful_count'],
        'casts' => ['rating' => 'integer', 'is_verified' => 'boolean', 'helpful_count' => 'integer'],
        'relationships' => [
            'user' => 'belongsTo:User',
            'recipe' => 'belongsTo:Recipe'
        ]
    ],
    'RecipeShare' => [
        'fillable' => ['user_id', 'recipe_id', 'platform'],
        'casts' => [],
        'relationships' => [
            'user' => 'belongsTo:User',
            'recipe' => 'belongsTo:Recipe'
        ]
    ],
    'RecipeComment' => [
        'fillable' => ['recipe_id', 'user_id', 'parent_id', 'comment', 'likes_count', 'is_approved'],
        'casts' => ['likes_count' => 'integer', 'is_approved' => 'boolean'],
        'traits' => ['SoftDeletes'],
        'relationships' => [
            'recipe' => 'belongsTo:Recipe',
            'user' => 'belongsTo:User',
            'parent' => 'belongsTo:RecipeComment,parent_id',
            'replies' => 'hasMany:RecipeComment,parent_id',
            'likes' => 'hasMany:CommentLike,comment_id'
        ]
    ],
    'CommentLike' => [
        'fillable' => ['user_id', 'comment_id'],
        'casts' => [],
        'relationships' => [
            'user' => 'belongsTo:User',
            'comment' => 'belongsTo:RecipeComment'
        ]
    ],
    'Admin' => [
        'fillable' => ['name', 'email', 'password', 'role', 'is_active', 'last_login_at'],
        'hidden' => ['password', 'remember_token'],
        'casts' => ['is_active' => 'boolean', 'last_login_at' => 'datetime', 'password' => 'hashed'],
        'relationships' => []
    ],
    'AnalyticsEvent' => [
        'fillable' => ['user_id', 'event_type', 'event_data', 'ip_address', 'user_agent'],
        'casts' => ['event_data' => 'array'],
        'relationships' => [
            'user' => 'belongsTo:User'
        ]
    ],
    'Subscription' => [
        'fillable' => ['user_id', 'plan_type', 'status', 'starts_at', 'ends_at', 'cancelled_at', 'stripe_subscription_id'],
        'casts' => ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'cancelled_at' => 'datetime'],
        'relationships' => [
            'user' => 'belongsTo:User'
        ]
    ],
    'Notification' => [
        'fillable' => ['user_id', 'type', 'message', 'data', 'is_read', 'read_at'],
        'casts' => ['data' => 'array', 'is_read' => 'boolean', 'read_at' => 'datetime'],
        'relationships' => [
            'user' => 'belongsTo:User'
        ]
    ],
];

echo "Model definitions ready. Total models: " . count($models) . "\n";
echo "This file contains the structure for all models.\n";
echo "Models will be filled manually using the edit tool.\n";
