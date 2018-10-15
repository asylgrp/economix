<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Matchable\MatchableInterface;

/**
 * Create balanceable matches
 */
final class BalanceableFactory implements MatchFactoryInterface
{
    public function createMatch(MatchableInterface ...$matchables): MatchInterface
    {
        return new BalanceableMatch(...$matchables);
    }
}
