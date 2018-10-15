<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\MatchInterface;
use asylgrp\matchmaker\Match\NonBalanceableMatch;

/**
 * Match items with amount zero
 */
final class ZeroAmountMatcher implements MatcherInterface
{
    public function match(MatchableInterface $needle, array $haystack): ?MatchInterface
    {
        if ($needle->getAmount()->isZero()) {
            return new NonBalanceableMatch($needle);
        }

        return null;
    }
}
