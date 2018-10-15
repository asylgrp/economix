<?php

namespace asylgrp\matchmaker\Match;

/**
 * Collection of matches
 */
interface MatchCollectionInterface extends \IteratorAggregate
{
    /**
     * @return MatchInterface[]
     */
    public function getMatches(): array;

    /**
     *  Implements the IteratorAggregate interface
     *
     * @return \Generator & iterable<MatchInterface>
     */
    public function getIterator(): \Generator;

    /**
     * @return \Generator & iterable<MatchInterface>
     */
    public function getSuccessful(): \Generator;

    /**
     * @return \Generator & iterable<MatchInterface>
     */
    public function getFailures(): \Generator;

    /**
     * @return \Generator & iterable<MatchInterface>
     */
    public function getBalanceables(): \Generator;
}
