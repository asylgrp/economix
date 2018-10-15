<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Matchable\MatchableInterface;

/**
 * Create non-balanceable matches
 */
final class NonBalanceableFactory implements MatchFactoryInterface
{
    public function createMatch(MatchableInterface ...$matchables): MatchInterface
    {
        return new NonBalanceableMatch(...$matchables);
    }
}
