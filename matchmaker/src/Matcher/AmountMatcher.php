<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Match\BalanceableFactory;

/**
 * Match based on amount
 */
final class AmountMatcher extends DateAndAmountMatcher
{
    public function __construct(AmountComparator $amountComparator)
    {
        parent::__construct(new DateComparator(365), $amountComparator, new BalanceableFactory);
    }
}
