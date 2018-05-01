<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

/**
 * Check if two dates are equal (within the max deviation nr of days setting)
 */
class DateComparator
{
    /**
     * @var int
     */
    private $maxDaysDeviation;

    public function __construct(int $maxDaysDeviation = 0)
    {
        $this->maxDaysDeviation = $maxDaysDeviation;
    }

    public function equals(\DateTimeInterface $left, \DateTimeInterface $right): bool
    {
        return (int)$left->diff($right, true)->format('%a') <= $this->maxDaysDeviation;
    }
}
