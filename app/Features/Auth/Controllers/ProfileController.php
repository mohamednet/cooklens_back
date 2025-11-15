<?php

namespace App\Features\Auth\Controllers;

use App\Features\Auth\Requests\UpdateProfileRequest;
use App\Features\Auth\Resources\UserResource;
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Get user profile.
     */
    public function show(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()->load(['providers', 'devices']))
        );
    }

    /**
     * Update user profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_url) {
                $this->fileUploadService->delete($user->avatar_url);
            }

            $data['avatar_url'] = $this->fileUploadService->upload(
                $request->file('avatar'),
                'avatars'
            );
            unset($data['avatar']);
        }

        $user->update($data);

        return $this->successResponse(
            new UserResource($user),
            'Profile updated successfully'
        );
    }

    /**
     * Delete user account.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        // Delete avatar if exists
        if ($user->avatar_url) {
            $this->fileUploadService->delete($user->avatar_url);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Soft delete user
        $user->delete();

        return $this->successResponse(null, 'Account deleted successfully');
    }
}
