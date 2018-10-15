<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Match;

/**
 * Collection of matches
 */
final class MatchCollection implements MatchCollectionInterface
{
    /**
     * @var MatchInterface[]
     */
    private $matches;

    public function __construct(MatchInterface ...$matches)
    {
        $this->matches = $matches;
    }

    public function getMatches(): array
    {
        return $this->matches;
    }

    public function getIterator(): \Generator
    {
        foreach ($this->getMatches() as $match) {
            yield $match;
        }
    }

    public function getSuccessful(): \Generator
    {
        foreach ($this->getIterator() as $match) {
            if ($match->isSuccess()) {
                yield $match;
            }
        }
    }

    public function getFailures(): \Generator
    {
        foreach ($this->getIterator() as $match) {
            if (!$match->isSuccess()) {
                yield $match;
            }
        }
    }

    public function getBalanceables(): \Generator
    {
        foreach ($this->getIterator() as $match) {
            if ($match->isBalanceable()) {
                yield $match;
            }
        }
    }
}
