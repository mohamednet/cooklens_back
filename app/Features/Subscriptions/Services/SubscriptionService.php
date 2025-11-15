<?php

namespace App\Services;

use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;

class SubscriptionService
{
    public function __construct(
        protected SubscriptionRepositoryInterface $subscriptionRepository
    ) {}

    /**
     * Create a new subscription.
     */
    public function create(array $data): Subscription
    {
        return $this->subscriptionRepository->create($data);
    }

    /**
     * Get user's active subscription.
     */
    public function getUserSubscription(int $userId): ?Subscription
    {
        return Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Check if user has active subscription.
     */
    public function hasActiveSubscription(int $userId): bool
    {
        return Subscription::where('user_id', $userId)
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->exists();
    }

    /**
     * Cancel subscription.
     */
    public function cancel(int $subscriptionId): bool
    {
        return $this->subscriptionRepository->update($subscriptionId, [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Upgrade subscription plan.
     */
    public function upgrade(int $subscriptionId, string $newPlan): bool
    {
        return $this->subscriptionRepository->update($subscriptionId, [
            'plan_type' => $newPlan,
        ]);
    }

    /**
     * Renew subscription.
     */
    public function renew(int $subscriptionId, int $days): bool
    {
        $subscription = Subscription::find($subscriptionId);

        if (! $subscription) {
            return false;
        }

        $newEndDate = $subscription->ends_at 
            ? $subscription->ends_at->addDays($days)
            : now()->addDays($days);

        return $this->subscriptionRepository->update($subscriptionId, [
            'ends_at' => $newEndDate,
            'status' => 'active',
        ]);
    }

    /**
     * Check if subscription is expired.
     */
    public function isExpired(Subscription $subscription): bool
    {
        return $subscription->ends_at && $subscription->ends_at->isPast();
    }
}
