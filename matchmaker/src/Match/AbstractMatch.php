<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Matchable\MatchableInterface;

abstract class AbstractMatch implements MatchInterface
{
    /**
     * @var MatchableInterface[]
     */
    private array $matched;

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
