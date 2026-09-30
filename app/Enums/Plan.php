<?php

namespace App\Enums;

/** A seller is on exactly one plan; Pro and Partner are exclusive (docs/01 "Plans and revenue"). */
enum Plan: string
{
    case Free = 'free';
    case Pro = 'pro';
    case Partner = 'partner';

    /** Pro and Partner both get top-of-results placement and the Featured badge. */
    public function isPaid(): bool
    {
        return $this !== self::Free;
    }
}
