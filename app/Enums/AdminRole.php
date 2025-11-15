<?php

namespace App\Enums;

enum AdminRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case EDITOR = 'editor';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::MODERATOR => 'Moderator',
            self::EDITOR => 'Editor',
        };
    }

    public function permissions(): array
    {
        return match($this) {
            self::SUPER_ADMIN => ['*'],
            self::ADMIN => ['manage_users', 'manage_recipes', 'manage_content', 'view_analytics'],
            self::MODERATOR => ['moderate_comments', 'moderate_recipes'],
            self::EDITOR => ['edit_recipes', 'edit_content'],
        };
    }
}
