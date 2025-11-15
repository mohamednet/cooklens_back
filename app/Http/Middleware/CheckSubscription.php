<?php

namespace App\Http\Middleware;

use App\Features\Subscriptions\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (! $this->subscriptionService->hasActiveSubscription($request->user()->id)) {
            return response()->json([
                'success' => false,
                'message' => 'This feature requires an active subscription.',
                'upgrade_required' => true,
            ], 403);
        }

        return $next($request);
    }
}
