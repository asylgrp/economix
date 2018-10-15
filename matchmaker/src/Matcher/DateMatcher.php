<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Match\NonBalanceableFactory;

/**
 * Match based on date
 */
final class DateMatcher extends DateAndAmountMatcher
{
    public function __construct(DateComparator $dateComparator)
    {
        parent::__construct($dateComparator, new AmountComparator(1.0), new NonBalanceableFactory);
    }
}
