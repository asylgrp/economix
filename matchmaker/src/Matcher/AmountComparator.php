<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use Money\Money;

/**
 * Check if two amounts are equal (within the max deviation quota setting)
 */
class AmountComparator
{
    private float $maxDeviationQuota;

    public function __construct(float $maxDeviationQuota = 0.0)
    {
        $this->maxDeviationQuota = $maxDeviationQuota;
    }

    /**
     * Defined as the sum of subj and obj divided by subj being less than or equal max deviation
     *
     * Note that zero is never equal to anything
     */
    public function equals(Money $subj, Money $obj): bool
    {
        if ($subj->isZero() || $obj->isZero()) {
            return false;
        }

        return abs((float)$subj->add($obj)->getAmount() / (float)$subj->getAmount()) <= $this->maxDeviationQuota;
    }
}
