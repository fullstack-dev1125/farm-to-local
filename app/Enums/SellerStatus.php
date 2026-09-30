<?php

namespace App\Enums;

enum SellerStatus: string
{
    case Seeded = 'seeded';       // imported from public records, unclaimed
    case Listed = 'listed';       // phone + email verified, producer attestation
    case Verified = 'verified';   // identity + one document reviewed by a human (rule 3)
    case Suspended = 'suspended';

    /** Statuses the radius search returns (docs/02 "Geo query"). */
    public static function searchable(): array
    {
        return [self::Listed->value, self::Verified->value];
    }
}
