<?php

namespace App\Enums;

enum TagType: string
{
    case DIETARY = 'dietary';
    case CUISINE = 'cuisine';
    case MEAL_TYPE = 'meal_type';
    case COOKING_METHOD = 'cooking_method';
    case OCCASION = 'occasion';
    case SEASON = 'season';

    public function label(): string
    {
        return match($this) {
            self::DIETARY => 'Dietary',
            self::CUISINE => 'Cuisine',
            self::MEAL_TYPE => 'Meal Type',
            self::COOKING_METHOD => 'Cooking Method',
            self::OCCASION => 'Occasion',
            self::SEASON => 'Season',
        };
    }
}
