<?php

namespace App\Features\Auth\Controllers;

use App\Features\Auth\Resources\TokenResource;
use App\Features\Auth\Services\TokenService;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected TokenService $tokenService
    ) {}

    /**
     * Get all active tokens.
     */
    public function index(Request $request): JsonResponse
    {
        $tokens = $this->tokenService->getActiveTokens($request->user());

        return $this->successResponse(
            TokenResource::collection($tokens)
        );
    }

    /**
     * Revoke a specific token.
     */
    public function destroy(Request $request, int $tokenId): JsonResponse
    {
        $revoked = $this->tokenService->revokeToken($request->user(), $tokenId);

        if (! $revoked) {
            return $this->notFoundResponse('Token not found');
        }

        return $this->successResponse(null, 'Token revoked successfully');
    }

    /**
     * Revoke all tokens except current.
     */
    public function destroyAll(Request $request): JsonResponse
    {
        $currentTokenId = $request->user()->currentAccessToken()->id;

        $request->user()->tokens()->where('id', '!=', $currentTokenId)->delete();

        return $this->successResponse(null, 'All other tokens revoked successfully');
    }
}
