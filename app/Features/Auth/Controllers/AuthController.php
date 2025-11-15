<?php

namespace App\Features\Auth\Controllers;

use App\Features\Auth\Requests\LoginRequest;
use App\Features\Auth\Requests\RegisterRequest;
use App\Features\Auth\Resources\UserResource;
use App\Features\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Registration successful', 201);
    }

    /**
     * Login user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->only(['email', 'password']));

        // Track device info
        if ($request->filled('device_name') || $request->filled('device_os')) {
            UserDevice::updateOrCreate(
                [
                    'user_id' => $result['user']->id,
                    'device_id' => $request->ip(),
                ],
                [
                    'device_name' => $request->input('device_name', 'Unknown'),
                    'device_os' => $request->input('device_os', 'Unknown'),
                    'last_login_at' => now(),
                ]
            );
        }

        return $this->successResponse([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 'Login successful');
    }

    /**
     * Logout user (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Logout from all devices (revoke all tokens).
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logoutAll($request->user());

        return $this->successResponse(null, 'Logged out from all devices successfully');
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user())
        );
    }
}
