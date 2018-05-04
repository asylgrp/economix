<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Match;

/**
 * Collection of matches
 */
class MatchCollection implements \IteratorAggregate
{
    /**
     * @var MatchInterface[]
     */
    private $matches;

    public function __construct(MatchInterface ...$matches)
    {
        $this->matches = $matches;
    }

    /**
     * @return MatchInterface[]
     */
    public function getMatches(): array
    {
        return $this->matches;
    }

    /**
     *  Implements the IteratorAggregate interface
     *
     * @return \Generator & iterable<MatchInterface>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->getMatches() as $match) {
            yield $match;
        }
    }

    /**
     * @return \Generator & iterable<MatchInterface>
     */
    public function getSuccessful(): \Generator
    {
        foreach ($this->getIterator() as $match) {
            if ($match->isSuccess()) {
                yield $match;
            }
        }
    }

    /**
     * @return \Generator & iterable<MatchInterface>
     */
    public function getFailures(): \Generator
    {
        foreach ($this->getIterator() as $match) {
            if (!$match->isSuccess()) {
                yield $match;
            }
        }
    }

    /**
     * @return \Generator & iterable<MatchInterface>
     */
    public function getBalanceables(): \Generator
    {
        foreach ($this->getIterator() as $match) {
            if ($match->isBalanceable()) {
                yield $match;
            }
        }
    }
}
