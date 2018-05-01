<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use byrokrat\amount\Amount;

/**
 * A match that can be automatically balanced if needed
 */
class BalanceableMatch implements MatchInterface
{
    /**
     * @var MatchableInterface[]
     */
    private $matched;

    public function __construct(MatchableInterface ...$matched)
    {
        $this->matched = $matched;
    }

    public function getMatched(): array
    {
        return $this->matched;
    }

    public function isBalanced(): bool
    {
        $amount = null;

        foreach ($this->getMatched() as $matched) {
            $amount = $amount ? $amount->add($matched->getAmount()) : $matched->getAmount();
        }

        return $amount ? $amount->isZero() : true;
    }

    public function isBalanceable(): bool
    {
        return !$this->isBalanced();
    }

    public function isSuccess(): bool
    {
        return $this->isBalanced() || $this->isBalanceable();
    }
}
