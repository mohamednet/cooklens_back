<?php

namespace App\Services;

use App\Models\User;

class TokenService
{
    /**
     * Create a new token for the user.
     */
    public function createToken(User $user, string $tokenName = 'auth_token'): string
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    /**
     * Revoke a specific token by ID.
     */
    public function revokeToken(User $user, int $tokenId): bool
    {
        return (bool) $user->tokens()->where('id', $tokenId)->delete();
    }

    /**
     * Revoke all tokens for the user.
     */
    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    /**
     * Get all active tokens for the user.
     */
    public function getActiveTokens(User $user)
    {
        return $user->tokens;
    }

    /**
     * Revoke current access token.
     */
    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
