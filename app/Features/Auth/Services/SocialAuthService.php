<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProvider;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SocialAuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Handle social authentication (Google, Apple, Facebook).
     */
    public function handleSocialAuth(string $provider, array $providerData): array
    {
        return DB::transaction(function () use ($provider, $providerData) {
            // Check if user exists with this provider
            $userProvider = UserProvider::where('provider_name', $provider)
                ->where('provider_id', $providerData['id'])
                ->first();

            if ($userProvider) {
                $user = $userProvider->user;
                
                // Update tokens
                $userProvider->update([
                    'access_token' => $providerData['token'] ?? null,
                    'refresh_token' => $providerData['refresh_token'] ?? null,
                    'expires_at' => $providerData['expires_at'] ?? null,
                ]);
            } else {
                // Check if user exists with this email
                $user = $this->userRepository->search(['email' => $providerData['email']])->first();

                if (! $user) {
                    // Create new user
                    $user = $this->userRepository->create([
                        'name' => $providerData['name'],
                        'email' => $providerData['email'],
                        'avatar_url' => $providerData['avatar'] ?? null,
                        'email_verified_at' => now(),
                    ]);
                }

                // Link provider to user
                UserProvider::create([
                    'user_id' => $user->id,
                    'provider_name' => $provider,
                    'provider_id' => $providerData['id'],
                    'access_token' => $providerData['token'] ?? null,
                    'refresh_token' => $providerData['refresh_token'] ?? null,
                    'expires_at' => $providerData['expires_at'] ?? null,
                ]);
            }

            $token = $user->createToken('social_auth_token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }
}
