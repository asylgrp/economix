<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use byrokrat\amount\Amount;

/**
 * Check if two amounts are equal (within the max deviation quota setting)
 */
class AmountComparator
{
    /**
     * @var float
     */
    private $maxDeviationQuota;

    public function __construct(float $maxDeviationQuota = 0.0)
    {
        $this->maxDeviationQuota = $maxDeviationQuota;
    }

    /**
     * Defined as the sum of subj and obj divided by subj being less than or equal max deviation
     *
     * Note that zero is never equal to anything
     */
    public function equals(Amount $subj, Amount $obj): bool
    {
        if ($subj->isZero() || $obj->isZero()) {
            return false;
        }

        return $subj->add($obj)->divideBy($subj)->getAbsolute()->getFloat() <= $this->maxDeviationQuota;
    }
}
