<?php

namespace App\Policies;

use App\Enums\AdminRole;
use App\Models\Admin;

class AdminPolicy
{
    /**
     * Determine if the admin can manage users.
     */
    public function manageUsers(Admin $admin): bool
    {
        return in_array($admin->role, [AdminRole::SUPER_ADMIN->value, AdminRole::ADMIN->value]);
    }

    /**
     * Determine if the admin can manage recipes.
     */
    public function manageRecipes(Admin $admin): bool
    {
        return in_array($admin->role, [
            AdminRole::SUPER_ADMIN->value,
            AdminRole::ADMIN->value,
            AdminRole::MODERATOR->value,
        ]);
    }

    /**
     * Determine if the admin can moderate comments.
     */
    public function moderateComments(Admin $admin): bool
    {
        return in_array($admin->role, [
            AdminRole::SUPER_ADMIN->value,
            AdminRole::ADMIN->value,
            AdminRole::MODERATOR->value,
        ]);
    }

    /**
     * Determine if the admin can view analytics.
     */
    public function viewAnalytics(Admin $admin): bool
    {
        return in_array($admin->role, [AdminRole::SUPER_ADMIN->value, AdminRole::ADMIN->value]);
    }

    /**
     * Determine if the admin can manage admins.
     */
    public function manageAdmins(Admin $admin): bool
    {
        return $admin->role === AdminRole::SUPER_ADMIN->value;
    }
}
