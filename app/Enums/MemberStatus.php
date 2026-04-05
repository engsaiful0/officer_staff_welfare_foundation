<?php

namespace App\Enums;

enum MemberStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case DORMANT = 'DORMANT';
    case FROZEN = 'FROZEN';
    case CLOSED = 'CLOSED';
    case RESTRICTED = 'RESTRICTED';
    case KYC_PENDING = 'KYC_PENDING';
    case SUSPENDED = 'SUSPENDED';
    case OVERDRAWN = 'OVERDRAWN';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::DORMANT => 'Dormant',
            self::FROZEN => 'Frozen',
            self::CLOSED => 'Closed',
            self::RESTRICTED => 'Restricted',
            self::KYC_PENDING => 'KYC pending',
            self::SUSPENDED => 'Suspended',
            self::OVERDRAWN => 'Overdrawn',
        };
    }
}
