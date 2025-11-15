<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case TRIAL = 'trial';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::CANCELLED => 'Cancelled',
            self::EXPIRED => 'Expired',
            self::TRIAL => 'Trial',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE || $this === self::TRIAL;
    }
}
