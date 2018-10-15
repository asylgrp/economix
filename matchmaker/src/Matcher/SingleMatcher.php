<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\MatchInterface;
use asylgrp\matchmaker\Match\NonBalanceableMatch;

/**
 * Fallback matcher matching needle with nothing
 */
final class SingleMatcher implements MatcherInterface
{
    public function match(MatchableInterface $needle, array $haystack): ?MatchInterface
    {
        return new NonBalanceableMatch($needle);
    }
}
