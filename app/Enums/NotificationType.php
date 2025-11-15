<?php

namespace App\Enums;

enum NotificationType: string
{
    case COMMENT = 'comment';
    case LIKE = 'like';
    case FOLLOW = 'follow';
    case RECIPE_PUBLISHED = 'recipe_published';
    case SUBSCRIPTION = 'subscription';
    case SYSTEM = 'system';

    public function label(): string
    {
        return match($this) {
            self::COMMENT => 'New Comment',
            self::LIKE => 'New Like',
            self::FOLLOW => 'New Follower',
            self::RECIPE_PUBLISHED => 'Recipe Published',
            self::SUBSCRIPTION => 'Subscription Update',
            self::SYSTEM => 'System Notification',
        };
    }
}
