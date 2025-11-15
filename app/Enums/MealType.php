<?php

namespace App\Enums;

enum MealType: string
{
    case BREAKFAST = 'breakfast';
    case LUNCH = 'lunch';
    case DINNER = 'dinner';
    case SNACK = 'snack';

    public function label(): string
    {
        return match($this) {
            self::BREAKFAST => 'Breakfast',
            self::LUNCH => 'Lunch',
            self::DINNER => 'Dinner',
            self::SNACK => 'Snack',
        };
    }
}
