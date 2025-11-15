<?php

namespace App\Features\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    use ApiResponse;

    /**
     * Send email verification notification.
     */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->errorResponse('Email already verified', 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->successResponse(null, 'Verification email sent');
    }

    /**
     * Verify email.
     */
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->successResponse(null, 'Email already verified');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->successResponse(null, 'Email verified successfully');
    }
}
