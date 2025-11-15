<?php

namespace App\Features\Auth\Controllers;

use App\Features\Auth\Requests\ForgotPasswordRequest;
use App\Features\Auth\Requests\ResetPasswordRequest;
use App\Features\Auth\Services\PasswordResetService;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PasswordResetService $passwordResetService
    ) {}

    /**
     * Send password reset link.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $message = $this->passwordResetService->sendResetLink($request->input('email'));

        return $this->successResponse(null, $message);
    }

    /**
     * Reset password.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $message = $this->passwordResetService->resetPassword($request->validated());

        return $this->successResponse(null, $message);
    }
}
