<?php

namespace App\Features\Auth\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Send password reset link to user's email.
     */
    public function sendResetLink(string $email): string
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    /**
     * Reset user's password.
     */
    public function resetPassword(array $credentials): string
    {
        $status = Password::reset($credentials, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();

            // Revoke all tokens
            $user->tokens()->delete();
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }
}
